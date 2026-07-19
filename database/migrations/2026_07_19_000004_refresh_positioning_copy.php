<?php

use App\Models\Experience;
use App\Models\Profile;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $profile = Profile::query()->find(1);

        if ($profile) {
            $profile->setTranslation('headline', 'es', 'Desarrollador de software · Backend, automatización e integraciones');
            $profile->setTranslation('headline', 'en', 'Software Developer · Backend, Automation and Integrations');
            $profile->setTranslation(
                'bio',
                'es',
                'Desarrollo aplicaciones internas, automatizaciones e integraciones entre ERP, ecommerce, inventario, proveedores y APIs. Trabajo principalmente con PHP, Laravel, bases de datos y servidores Linux, desde el análisis del problema hasta el despliegue y la mejora continua.'
            );
            $profile->setTranslation(
                'bio',
                'en',
                'I build internal applications, automations and integrations across ERP, ecommerce, inventory, suppliers and APIs. I work mainly with PHP, Laravel, databases and Linux servers, from problem analysis through deployment and continuous improvement.'
            );
            $profile->setTranslation(
                'about_long',
                'es',
                "Soy desarrollador de software especializado en aplicaciones internas, automatización e integración de sistemas empresariales.\n\nHe trabajado en desarrollo web, plataformas educativas, soporte tecnológico e infraestructura. Actualmente centro mi trabajo en conectar ERP y ecommerce, organizar catálogos de proveedores, mejorar el control de inventario y transformar tareas manuales en procesos más fiables.\n\nUtilizo herramientas de inteligencia artificial como apoyo para analizar, desarrollar, revisar y documentar soluciones, manteniendo la validación técnica y la responsabilidad sobre el resultado."
            );
            $profile->setTranslation(
                'about_long',
                'en',
                "I am a software developer specialised in internal applications, automation and business system integration.\n\nI have worked on web development, educational platforms, technical support and infrastructure. Today I focus on connecting ERP and ecommerce, organising supplier catalogs, improving inventory control and turning manual tasks into more reliable processes.\n\nI use AI tools to support analysis, development, review and documentation, while keeping technical validation and ownership of the result."
            );
            $profile->setTranslation('availability', 'es', 'Disponible para oportunidades profesionales y proyectos seleccionados.');
            $profile->setTranslation('availability', 'en', 'Open to professional opportunities and selected projects.');
            $profile->save();
        }

        $experience = Experience::query()
            ->where('company', 'Musical Princesa')
            ->orderBy('sort')
            ->first();

        if ($experience) {
            $experience->setTranslation('role', 'es', 'Informático · Desarrollo de software y sistemas internos');
            $experience->setTranslation('role', 'en', 'IT specialist · Software development and internal systems');
            $experience->setTranslation(
                'description',
                'es',
                'Desarrollo y evolución de aplicaciones internas, automatización de catálogos, integraciones entre Dolibarr y PrestaShop, control de inventario, gestión de proveedores e infraestructura tecnológica. También realizo diagnóstico de incidencias, administración de servidores y mejora de procesos operativos.'
            );
            $experience->setTranslation(
                'description',
                'en',
                'Development and evolution of internal applications, catalog automation, integrations between Dolibarr and PrestaShop, inventory control, supplier management and technology infrastructure. I also diagnose incidents, administer servers and improve operational processes.'
            );
            $experience->save();
        }
    }

    public function down(): void
    {
        // Content refresh; no destructive rollback of narrative copy.
    }
};
