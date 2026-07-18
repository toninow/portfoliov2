<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('headline')->nullable();
            $table->json('bio')->nullable();
            $table->json('about_long')->nullable();
            $table->json('availability')->nullable();
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('avatar_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->json('degree')->nullable();
            $table->json('extras')->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('label')->nullable();
            $table->string('url');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->json('label');
            $table->string('url');
            $table->string('location')->default('header');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('body')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('area')->default('tools');
            $table->string('icon_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('skill_groups', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('icon_path')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->json('problems')->nullable();
            $table->json('includes')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('use_cases')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->json('problem')->nullable();
            $table->json('context')->nullable();
            $table->json('constraints')->nullable();
            $table->json('solution')->nullable();
            $table->json('process')->nullable();
            $table->json('decisions')->nullable();
            $table->json('result')->nullable();
            $table->json('improvements')->nullable();
            $table->json('role')->nullable();
            $table->string('period')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('project_type')->nullable();
            $table->string('repository_url')->nullable();
            $table->string('url')->nullable();
            $table->string('main_image_path')->nullable();
            $table->string('status')->default('draft');
            $table->string('visibility')->default('public');
            $table->boolean('is_featured')->default(false);
            $table->string('featured_size')->default('compact');
            $table->unsignedInteger('sort')->default(0);
            $table->json('seo')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility']);
            $table->index('is_featured');
            $table->index('year');
        });

        Schema::create('project_technology', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'technology_id']);
        });

        Schema::create('service_technology', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['service_id', 'technology_id']);
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->json('alt')->nullable();
            $table->json('caption')->nullable();
            $table->string('type')->default('gallery');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('project_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->string('value')->nullable();
            $table->string('unit')->nullable();
            $table->json('description')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->json('role');
            $table->string('company')->nullable();
            $table->string('location')->nullable();
            $table->json('description')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('institution')->nullable();
            $table->json('description')->nullable();
            $table->string('start_year')->nullable();
            $table->string('end_year')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('issuer')->nullable();
            $table->string('issued_at')->nullable();
            $table->string('credential_url')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('alt')->nullable();
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('country')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('source')->default('website');
            $table->string('status')->default('new');
            $table->string('need_type')->nullable();
            $table->string('estimated_value')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('next_follow_up_at');
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('note');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending');
            $table->string('priority')->default('normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('education');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('project_metrics');
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('service_technology');
        Schema::dropIfExists('project_technology');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_groups');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('site_settings');
    }
};
