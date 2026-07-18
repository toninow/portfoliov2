<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // First post: a genuine, first-person starting point for Antonio's
        // personal-change journey. All text is editable from the admin panel.
        Post::updateOrCreate(
            ['slug' => 'el-dia-que-decidi-cambiar'],
            [
                'title' => [
                    'es' => 'El día que decidí cambiar',
                    'en' => 'The day I decided to change',
                ],
                'topic' => [
                    'es' => 'Cambio personal',
                    'en' => 'Personal change',
                ],
                'excerpt' => [
                    'es' => 'Peso 150 kg y necesito un cambio de 180 grados en mi vida. Este es el punto de partida: aquí voy a contar el proceso, sin filtros.',
                    'en' => 'I weigh 150 kg and I need a complete turnaround in my life. This is the starting point: I will document the process here, no filters.',
                ],
                'body' => [
                    'es' => <<<'MD'
Llevo tiempo aplazando esta decisión. Hoy la escribo para que sea real.

Peso **150 kg**. No es solo un número: es cansancio, es ropa que no me sirve, es evitar fotos y planes. Sé que necesito un cambio de 180 grados, y sé que no va a pasar de un día para otro.

## Por qué lo hago público

Trabajo construyendo sistemas: mido, itero y mejoro. Voy a tratar mi salud igual. Escribir aquí me obliga a rendir cuentas y, si a alguien le sirve para empezar lo suyo, mejor.

## El plan, en simple

- **Comer mejor**, sin dietas imposibles.
- **Moverme cada día**, empezando por caminar.
- **Dormir y descansar** de verdad.
- **Medir el progreso** de forma honesta, no perfecta.

No voy a inventar cifras ni prometer resultados rápidos. Voy a contar lo que hago, lo que funciona y lo que no.

> El primer paso no es el gimnasio. Es decidir que hoy empieza.

Nos vemos en la próxima entrada.
MD,
                    'en' => <<<'MD'
I have been putting off this decision for a long time. Today I write it down to make it real.

I weigh **150 kg**. It is not just a number: it is fatigue, clothes that no longer fit, avoiding photos and plans. I know I need a complete turnaround, and I know it will not happen overnight.

## Why I am making it public

I build systems for a living: I measure, iterate and improve. I am going to treat my health the same way. Writing here keeps me accountable, and if it helps someone else start their own, even better.

## The plan, kept simple

- **Eat better**, without impossible diets.
- **Move every day**, starting with walking.
- **Sleep and rest** properly.
- **Track progress** honestly, not perfectly.

I will not invent numbers or promise quick results. I will share what I do, what works and what does not.

> The first step is not the gym. It is deciding that today is the day.

See you in the next post.
MD,
                ],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'sort' => 0,
            ]
        );
    }
}
