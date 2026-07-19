<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Antonio Benalcázar')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.4rem')
            ->colors([
                'primary' => Color::Blue,
                'info' => Color::Cyan,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->navigationGroups([
                'Sistema',
                'Contenido',
                'CRM',
                'Configuración',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
            ])
            ->databaseNotifications()
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.login-brand')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => '<meta name="robots" content="noindex,nofollow">',
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => view('filament.admin-styles')->render(),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
