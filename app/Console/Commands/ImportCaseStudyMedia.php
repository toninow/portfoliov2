<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCaseStudyMedia extends Command
{
    protected $signature = 'portfolio:import-case-media
        {--base=https://www.servidormp.com/downloads/gitea-screenshots : Base URL of the media pack}
        {--force : Replace existing gallery images and covers}';

    protected $description = 'Import screenshots and demo videos from the Musical Princesa media pack into case studies';

    /**
     * Local portfolio slug => remote module id + selected screenshots (prefer full UI, skip *-lista).
     *
     * @var array<string, array{module: string, cover: string, shots: list<string>, captions?: array<string, array{es: string, en: string}>, video?: string, tech?: list<string>}>
     */
    protected array $map = [
        'mp-proveedores' => [
            // Source: #dolibarr-module-mp-proveedores-dolv5
            'module' => 'dolibarr-module-mp-proveedores-dolv5',
            'cover' => '02-salud-casos.png',
            'shots' => [
                '02-salud-casos.png',
                '03-lista-casos.png',
                '04-vinculacion.png',
                '05-enlaces.png',
                '06-sync.png',
                '07-runs.png',
                '08-api.png',
                '09-mapeo-proveedores.png',
                '12-detalle-caso-conflicto.png',
                '13-detalle-caso-duplicado-ean.png',
                '16-lista-casos-filtrada.png',
                '21-filtro-panel-activo.png',
                '27-filtro-final-overview.png',
            ],
            'video' => 'videos/dolibarr-module-mp-proveedores-dolv5.mp4',
        ],
        'control-stock-dolibarr' => [
            // Source pack id: web-control-stock-dolibarr — public name stays "Control de stock Dolibarr".
            'module' => 'web-control-stock-dolibarr',
            'cover' => '14-home-stock-final.png',
            'shots' => [
                '14-home-stock-final.png',
                '01-login-stock-mp.png',
                '01-catalogo-stock-vivo.png',
                '03-buscar-producto-stock.png',
                '04-ficha-revision-stock.png',
                '05-controles-corregir-stock.png',
                '08-busqueda-por-referencia.png',
                '09-detalle-desde-busqueda.png',
                '10-escaner-ean-manual.png',
                '12-perfil-actividad-stock.png',
                '13-mis-productos-revisados.png',
            ],
            'captions' => [
                '14-home-stock-final.png' => ['es' => 'Inicio', 'en' => 'Home'],
                '01-login-stock-mp.png' => ['es' => 'Acceso', 'en' => 'Login'],
                '01-catalogo-stock-vivo.png' => ['es' => 'Catálogo de stock', 'en' => 'Live stock catalog'],
                '03-buscar-producto-stock.png' => ['es' => 'Búsqueda de producto', 'en' => 'Product search'],
                '04-ficha-revision-stock.png' => ['es' => 'Ficha de revisión', 'en' => 'Review sheet'],
                '05-controles-corregir-stock.png' => ['es' => 'Corrección de stock', 'en' => 'Stock correction'],
                '08-busqueda-por-referencia.png' => ['es' => 'Búsqueda por referencia', 'en' => 'Search by reference'],
                '09-detalle-desde-busqueda.png' => ['es' => 'Detalle del producto', 'en' => 'Product detail'],
                '10-escaner-ean-manual.png' => ['es' => 'Escáner EAN', 'en' => 'EAN scanner'],
                '12-perfil-actividad-stock.png' => ['es' => 'Actividad del perfil', 'en' => 'Profile activity'],
                '13-mis-productos-revisados.png' => ['es' => 'Productos revisados', 'en' => 'Reviewed products'],
            ],
            'video' => 'videos/web-control-stock-dolibarr.mp4',
            'tech' => ['php', 'javascript', 'dolibarr', 'apis-rest'],
        ],
        'integracion-prestashop-dolibarr' => [
            'module' => 'prestashop-module-mp-dolipresta-debug',
            'cover' => '01-admin-debug.png',
            'shots' => [
                '01-admin-debug.png',
                '02-admin-configuracion.png',
            ],
            'video' => 'videos/prestashop-module-mp-dolipresta-debug.mp4',
        ],
        // automatizacion-catalogos-proveedores: no separate pack; V5 media belongs to mp-proveedores.
    ];

    public function handle(): int
    {
        $base = rtrim((string) $this->option('base'), '/');
        $force = (bool) $this->option('force');
        $disk = Storage::disk('public');

        foreach ($this->map as $slug => $config) {
            $project = Project::query()->where('slug', $slug)->first();
            if (! $project) {
                $this->warn("Skip {$slug}: project not found");

                continue;
            }

            $this->info("Importing media for {$slug}");

            if ($force) {
                foreach ($project->images as $image) {
                    if ($image->path && $disk->exists($image->path)) {
                        $disk->delete($image->path);
                    }
                    $image->delete();
                }
            } elseif ($project->images()->exists() && $project->main_image_path) {
                $this->line('  already has media (use --force to replace)');

                continue;
            }

            $coverRelative = null;
            $sort = 0;

            foreach ($config['shots'] as $filename) {
                $url = "{$base}/modulos/{$config['module']}/{$filename}";
                $dest = "projects/gallery/{$slug}/".Str::slug(pathinfo($filename, PATHINFO_FILENAME)).'.png';

                if (! $this->download($url, $dest, $disk)) {
                    $this->warn("  failed shot {$filename}");

                    continue;
                }

                if ($filename === $config['cover']) {
                    $coverDest = "projects/{$slug}-cover.png";
                    $disk->put($coverDest, $disk->get($dest));
                    $coverRelative = $coverDest;
                }

                $labels = $this->labelsFor($filename, $config['captions'][$filename] ?? null);
                ProjectImage::create([
                    'project_id' => $project->id,
                    'path' => $dest,
                    'alt' => [
                        'es' => $labels['es'].' — '.$project->getTranslation('name', 'es'),
                        'en' => $labels['en'].' — '.$project->getTranslation('name', 'en'),
                    ],
                    'caption' => [
                        'es' => $labels['es'],
                        'en' => $labels['en'],
                    ],
                    'type' => str_contains($filename, 'detalle') || str_contains($filename, 'panel') ? 'desktop' : 'gallery',
                    'is_featured' => $filename === $config['cover'],
                    'is_visible' => true,
                    'sort' => $sort++,
                ]);
                $this->line("  + {$filename}");
            }

            if ($coverRelative) {
                $project->main_image_path = $coverRelative;
            }

            if (! empty($config['video'])) {
                $videoUrl = "{$base}/{$config['video']}";
                $videoDest = 'projects/videos/'.$slug.'.mp4';
                if ($this->download($videoUrl, $videoDest, $disk)) {
                    $project->demo_video_path = $videoDest;
                    $this->line('  + video');
                } else {
                    $this->warn('  video download failed');
                }
            }

            $project->save();

            if (! empty($config['tech'])) {
                $ids = Technology::query()->whereIn('slug', $config['tech'])->pluck('id');
                if ($ids->isNotEmpty()) {
                    $project->technologies()->sync($ids);
                    $this->line('  tech stack synced');
                }
            }
        }

        if ($force) {
            $this->clearProjectMedia('automatizacion-catalogos-proveedores', $disk);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    protected function clearProjectMedia(string $slug, $disk): void
    {
        $project = Project::query()->where('slug', $slug)->first();
        if (! $project) {
            return;
        }

        $this->info("Clearing media for {$slug} (reassigned pack)");

        foreach ($project->images as $image) {
            if ($image->path && $disk->exists($image->path)) {
                $disk->delete($image->path);
            }
            $image->delete();
        }

        foreach (array_filter([$project->main_image_path, $project->demo_video_path]) as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }

        $project->forceFill([
            'main_image_path' => null,
            'demo_video_path' => null,
        ])->save();
    }

    protected function download(string $url, string $dest, $disk): bool
    {
        try {
            $response = Http::timeout(120)
                ->withHeaders(['User-Agent' => 'antoniobc-portfolio-importer/1.0'])
                ->get($url);

            if (! $response->successful() || strlen($response->body()) < 1000) {
                return false;
            }

            $disk->put($dest, $response->body());

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param  array{es?: string, en?: string}|null  $override
     * @return array{es: string, en: string}
     */
    protected function labelsFor(string $filename, ?array $override): array
    {
        $fallback = $this->captionFromFilename($filename);

        return [
            'es' => $override['es'] ?? $fallback,
            'en' => $override['en'] ?? $fallback,
        ];
    }

    protected function captionFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = preg_replace('/^\d+-/', '', $name) ?? $name;
        $name = str_replace(['-', '_'], ' ', $name);

        return Str::of($name)->title()->toString();
    }
}
