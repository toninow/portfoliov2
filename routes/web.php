<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
 * Public routes are registered twice: once at the root for the default
 * locale (Spanish) and once under the /en prefix for English. English
 * route names are prefixed with "en." so the language switcher can map
 * between the two variants (see App\Support\Locale).
 */
$publicRoutes = function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/proyectos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/proyectos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');
    Route::get('/sobre-mi', AboutController::class)->name('about');
    Route::get('/contacto', [ContactController::class, 'index'])->name('contact');
    Route::post('/contacto', [ContactController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('contact.store');
    Route::get('/cv', CvController::class)->name('cv');
};

Route::middleware('setlocale:es')->group($publicRoutes);

Route::prefix('en')->name('en.')->middleware('setlocale:en')->group($publicRoutes);

// Redirect explicit /es/* to the canonical root URLs (avoid duplicate content).
Route::get('/es/{path?}', fn (string $path = '') => redirect('/'.$path))->where('path', '.*');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Local-only helper to preview the authenticated admin panel (e.g. for QA screenshots).
if (app()->environment('local')) {
    Route::get('/__dev-login', function () {
        $user = User::whereIn('role', User::ROLES)->first();
        if ($user) {
            auth()->login($user);
        }

        return redirect('/admin');
    });
}
