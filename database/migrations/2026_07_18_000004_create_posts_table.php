<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('excerpt')->nullable();
            $table->json('body')->nullable();
            $table->json('topic')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('reading_minutes')->nullable();
            $table->json('seo')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
