<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_loads(): void
    {
        Post::create([
            'slug' => 'hola-mundo',
            'title' => ['es' => 'Hola mundo', 'en' => 'Hello world'],
            'excerpt' => ['es' => 'Primera entrada', 'en' => 'First post'],
            'body' => ['es' => 'Contenido de prueba.', 'en' => 'Test content.'],
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog')->assertOk()->assertSee('Hola mundo');
    }

    public function test_published_post_detail_loads(): void
    {
        Post::create([
            'slug' => 'mi-entrada',
            'title' => ['es' => 'Mi entrada', 'en' => 'My post'],
            'body' => ['es' => '## Sección\n\nTexto.', 'en' => '## Section\n\nText.'],
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/mi-entrada')->assertOk()->assertSee('Mi entrada');
    }

    public function test_draft_post_is_not_publicly_accessible(): void
    {
        Post::create([
            'slug' => 'borrador',
            'title' => ['es' => 'Borrador', 'en' => 'Draft'],
            'status' => 'draft',
        ]);

        $this->get('/blog/borrador')->assertNotFound();
    }
}
