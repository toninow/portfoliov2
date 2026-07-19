<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $project = DB::table('projects')->where('slug', 'control-stock-dolibarr')->first();
        if (! $project) {
            return;
        }

        DB::table('projects')->where('id', $project->id)->update([
            'role' => json_encode([
                'es' => 'Producto, backend de integración y app web',
                'en' => 'Product, integration backend and web app',
            ], JSON_UNESCAPED_UNICODE),
            'responsibilities' => json_encode([
                'es' => 'Diseño mobile-first web, integración con la API de Dolibarr, autenticación, roles y modo de consulta.',
                'en' => 'Mobile-first web design, Dolibarr API integration, authentication, roles and read-focused lookup mode.',
            ], JSON_UNESCAPED_UNICODE),
            'solution' => json_encode([
                'es' => 'Aplicación web mobile-first con búsqueda por EAN, UPC, referencia y nombre, stock por almacén, integración con la API de Dolibarr y control de permisos orientado a consulta.',
                'en' => 'Mobile-first web app with search by EAN, UPC, reference and name, stock by warehouse, Dolibarr API integration and permission control focused on lookup.',
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Intentional no-op: content correction should not be reverted.
    }
};
