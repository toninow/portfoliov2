<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SitePreview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $navigationLabel = 'Vista previa del sitio';

    protected static string|\UnitEnum|null $navigationGroup = 'Panel';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Vista previa del sitio';

    protected string $view = 'filament.pages.site-preview';

    public array $devices = [
        'desktop' => ['label' => 'Escritorio', 'width' => 1440],
        'laptop' => ['label' => 'Portátil', 'width' => 1280],
        'tablet' => ['label' => 'Tablet', 'width' => 768],
        'mobile-430' => ['label' => 'Móvil 430', 'width' => 430],
        'mobile-390' => ['label' => 'Móvil 390', 'width' => 390],
        'mobile-360' => ['label' => 'Móvil 360', 'width' => 360],
    ];

    public array $pages = [
        '/' => 'Inicio',
        '/proyectos' => 'Proyectos',
        '/servicios' => 'Servicios',
        '/sobre-mi' => 'Sobre mí',
        '/contacto' => 'Contacto',
    ];
}
