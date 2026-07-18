@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $l = app()->getLocale();
@endphp

<x-layout :title="__('portfolio.blog.title')" :description="__('portfolio.blog.lead')">

    <section class="section">
        <div class="container-page">
            <p class="eyebrow">{{ __('portfolio.blog.eyebrow') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-bold text-balance">{{ __('portfolio.blog.title') }}</h1>
            <p class="mt-5 text-lg text-[var(--color-muted)] max-w-2xl">{{ __('portfolio.blog.lead') }}</p>

            @if($posts->isEmpty())
                <div class="mt-12 card p-10 text-center text-[var(--color-muted)]">
                    {{ __('portfolio.blog.empty') }}
                </div>
            @else
                <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($posts as $post)
                        @php
                            $cover = $post->cover_image_path ? Storage::url($post->cover_image_path) : null;
                            $topic = $post->getTranslation('topic', $l);
                            $excerpt = $post->getTranslation('excerpt', $l);
                        @endphp
                        <a href="{{ Locale::route('blog.show', $post) }}"
                           class="group card overflow-hidden flex flex-col transition-transform duration-200 hover:-translate-y-1">
                            <div class="relative aspect-[16/9] overflow-hidden bg-[var(--color-elevated)]">
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $post->getTranslation('title', $l) }}" loading="lazy" decoding="async"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full grid place-items-center text-[var(--color-muted)] font-mono text-xs">{{ $topic ?: 'Blog' }}</div>
                                @endif
                                @if($topic)
                                    <span class="absolute top-3 left-3 chip bg-[var(--color-bg)]/70">{{ $topic }}</span>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col grow">
                                <div class="flex items-center gap-2 text-xs text-[var(--color-muted)] font-mono">
                                    @if($post->published_at)<span>{{ $post->published_at->translatedFormat('d M Y') }}</span>@endif
                                    <span aria-hidden="true">·</span>
                                    <span>{{ $post->readingMinutes($l) }} {{ __('portfolio.blog.min_read') }}</span>
                                </div>
                                <h2 class="mt-2 font-display font-semibold text-lg text-[var(--color-ink)]">{{ $post->getTranslation('title', $l) }}</h2>
                                @if($excerpt)
                                    <p class="mt-2 text-sm text-[var(--color-muted)] line-clamp-3">{{ Str::limit($excerpt, 150) }}</p>
                                @endif
                                <span class="mt-4 inline-flex items-center gap-1 text-sm text-[var(--color-brand-bright)] font-medium">
                                    {{ __('portfolio.blog.read') }}
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"
                                         class="transition-transform group-hover:translate-x-1"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>

</x-layout>
