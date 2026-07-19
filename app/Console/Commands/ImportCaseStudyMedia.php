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
     * @var array<string, array{module: string, cover: string, shots: list<string>, video?: string, tech?: list<string>}>
     */
    protected array $map = [
        'mp-proveedores' => [
            'module' => 'dolibarr-module-mp-proveedores-dol',
            'cover' => '02-index-panel.png',
            'shots' => [
                '02-index-panel.png',
                '03-lista-productos-proveedor.png',
                '04-productos-dolibarr.png',
                '05-auditoria-producto.png',
                '07-vinculacion.png',
                '08-conflictos-ean.png',
                '09-exportacion.png',
                '13-detalle-producto.png',
            ],
            'video' => 'videos/dolibarr-module-mp-proveedores-dol.mp4',
        ],
        'control-stock-dolibarr' => [
            'module' => 'web-control-stock-dolibarr',
            'cover' => '01-aplicacion.png',
            'shots' => [
                '01-aplicacion.png',
            ],
            'video' => 'videos/web-control-stock-dolibarr.mp4',
            // Confirmed webapp in the media pack (not Flutter).
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
        'automatizacion-catalogos-proveedores' => [
            'module' => 'dolibarr-module-mp-proveedores-dolv5',
            'cover' => '03-lista-casos.png',
            'shots' => [
                '02-salud-casos.png',
                '03-lista-casos.png',
                '04-vinculacion.png',
                '06-sync.png',
                '07-runs.png',
                '11-detalle-caso-producto.png',
            ],
            'video' => 'videos/dolibarr-module-mp-proveedores-dolv5.mp4',
        ],
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

                $label = $this->captionFromFilename($filename);
                ProjectImage::create([
                    'project_id' => $project->id,
                    'path' => $dest,
                    'alt' => [
                        'es' => $label.' — '.$project->getTranslation('name', 'es'),
                        'en' => $label.' — '.$project->getTranslation('name', 'en'),
                    ],
                    'caption' => [
                        'es' => $label,
                        'en' => $label,
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

        $this->info('Done.');

        return self::SUCCESS;
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

    protected function captionFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = preg_replace('/^\d+-/', '', $name) ?? $name;
        $name = str_replace(['-', '_'], ' ', $name);

        return Str::of($name)->title()->toString();
    }
}
