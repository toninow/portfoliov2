<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SitePreview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $navigationLabel = 'Vista previa del sitio';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Vista previa del sitio';

    protected string $view = 'filament.pages.site-preview';

    /**
     * Device presets used by the live preview iframe.
     *
     * @var array<string, array{label: string, width: int, height: int, frame: string}>
     */
    public array $devices = [
        'desktop' => [
            'label' => 'Escritorio',
            'width' => 1440,
            'height' => 900,
            'frame' => 'desktop',
        ],
        'tablet' => [
            'label' => 'Tablet',
            'width' => 768,
            'height' => 1024,
            'frame' => 'tablet',
        ],
        'mobile' => [
            'label' => 'Móvil',
            'width' => 390,
            'height' => 844,
            'frame' => 'mobile',
        ],
        'mobile-sm' => [
            'label' => 'Móvil pequeño',
            'width' => 360,
            'height' => 740,
            'frame' => 'mobile',
        ],
    ];

    public array $pages = [
        '/' => 'Inicio',
        '/proyectos' => 'Proyectos',
        '/proyectos/mp-proveedores' => 'Caso: MP Proveedores',
        '/proyectos/control-stock-dolibarr' => 'Caso: Control de stock',
        '/servicios' => 'Servicios',
        '/sobre-mi' => 'Sobre mí',
        '/contacto' => 'Contacto',
        '/blog' => 'Blog',
    ];

    public function getHeading(): string
    {
        return 'Vista previa del sitio';
    }

    public function getSubheading(): ?string
    {
        return 'Comprueba cómo se ve la web pública en escritorio, tablet y móvil. Las media queries usan el ancho real del dispositivo simulado.';
    }
}
