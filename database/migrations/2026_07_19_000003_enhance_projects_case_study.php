<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Non-destructive enrichment of the projects case-study model.
 * Existing columns and data are preserved; new flags are backfilled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_case_study')->default(false)->after('is_featured');
            $table->boolean('is_archived')->default(false)->after('is_case_study');
            $table->string('lifecycle')->default('completed')->after('status');
            $table->string('confidentiality_level')->default('public')->after('visibility');
            $table->boolean('is_ongoing')->default(false)->after('period');
            $table->json('outcome_headline')->nullable()->after('summary');
            $table->json('responsibilities')->nullable()->after('role');
            $table->json('learnings')->nullable()->after('improvements');
            $table->json('architecture_description')->nullable()->after('decisions');
            $table->json('workflow_steps')->nullable();
            $table->json('features')->nullable();
            $table->json('technical_decisions')->nullable();
            $table->json('challenges')->nullable();
            $table->json('qualitative_results')->nullable();
            $table->json('external_links')->nullable();
            $table->unsignedTinyInteger('completeness_score')->default(0)->after('sort');
        });

        Schema::table('project_metrics', function (Blueprint $table) {
            $table->boolean('is_approximate')->default(false)->after('is_public');
        });

        Schema::table('project_images', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('type');
            $table->boolean('is_visible')->default(true)->after('is_featured');
        });

        // Backfill: featured → case studies; everything else → archive.
        DB::table('projects')->where('is_featured', true)->update([
            'is_case_study' => true,
            'is_archived' => false,
            'lifecycle' => 'production',
        ]);

        DB::table('projects')->where('is_featured', false)->update([
            'is_case_study' => false,
            'is_archived' => true,
            'lifecycle' => 'historical',
        ]);

        // Projects already marked archived keep archive flags.
        DB::table('projects')->where('status', 'archived')->orWhere('visibility', 'archived')->update([
            'is_archived' => true,
            'lifecycle' => 'historical',
        ]);

        // Known lifecycle corrections (no invented metrics).
        DB::table('projects')->where('slug', 'backups-restic')->update([
            'lifecycle' => 'implementation',
            'is_ongoing' => true,
        ]);

        DB::table('projects')->where('slug', 'gitea-autogestionado')->update([
            'lifecycle' => 'production',
            'year' => 2026,
            'period' => '2026',
        ]);

        DB::table('projects')->where('slug', 'control-stock-dolibarr')->update([
            'lifecycle' => 'production',
        ]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_case_study',
                'is_archived',
                'lifecycle',
                'confidentiality_level',
                'is_ongoing',
                'outcome_headline',
                'responsibilities',
                'learnings',
                'architecture_description',
                'workflow_steps',
                'features',
                'technical_decisions',
                'challenges',
                'qualitative_results',
                'external_links',
                'completeness_score',
            ]);
        });

        Schema::table('project_metrics', function (Blueprint $table) {
            $table->dropColumn('is_approximate');
        });

        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_visible']);
        });
    }
};
