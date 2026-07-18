@php
    use App\Support\Locale;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    $l = app()->getLocale();
    $title = $post->getTranslation('title', $l);
    $excerpt = $post->getTranslation('excerpt', $l);
    $topic = $post->getTranslation('topic', $l);
    $cover = $post->cover_image_path ? Storage::url($post->cover_image_path) : null;
    $bodyMd = $post->getTranslation('body', $l);
    $seoTitle = data_get($post->seo, $l.'.title') ?: $title;
    $seoDesc = data_get($post->seo, $l.'.description') ?: $excerpt;
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $title,
        'description' => $excerpt,
        'datePublished' => optional($post->published_at)->toIso8601String(),
        'dateModified' => $post->updated_at?->toIso8601String(),
        'author' => ['@type' => 'Person', 'name' => $siteProfile->name],
        'mainEntityOfPage' => url()->current(),
    ];
    if ($cover) {
        $jsonLd['image'] = url($cover);
    }
@endphp

<x-layout :title="$seoTitle" :description="$seoDesc" :ogImage="$cover" :jsonLd="$jsonLd">

    <article class="section">
        <div class="container-page max-w-3xl">
            <a href="{{ Locale::route('blog.index') }}" class="inline-flex items-center gap-1 text-sm text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                {{ __('portfolio.blog.back') }}
            </a>

            <div class="mt-6 flex items-center gap-2 text-xs text-[var(--color-muted)] font-mono">
                @if($topic)<span class="chip">{{ $topic }}</span>@endif
                @if($post->published_at)<span>{{ $post->published_at->translatedFormat('d F Y') }}</span>@endif
                <span aria-hidden="true">·</span>
                <span>{{ $post->readingMinutes($l) }} {{ __('portfolio.blog.min_read') }}</span>
            </div>

            <h1 class="mt-4 text-3xl sm:text-4xl lg:text-[2.6rem] leading-tight font-bold text-balance">{{ $title }}</h1>
            @if($excerpt)
                <p class="mt-5 text-lg text-[var(--color-muted)]">{{ $excerpt }}</p>
            @endif
        </div>

        @if($cover)
            <div class="container-page max-w-4xl mt-8">
                <img src="{{ $cover }}" alt="{{ $title }}" class="w-full rounded-2xl border border-[var(--color-line)]">
            </div>
        @endif

        <div class="container-page max-w-3xl">
            @if($bodyMd)
                <div class="prose-blog mt-10">
                    {!! Str::markdown($bodyMd, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                </div>
            @endif

            <div class="mt-12 surface p-8 text-center" style="box-shadow: var(--shadow-glow);">
                <h2 class="text-xl sm:text-2xl font-bold">{{ __('portfolio.blog.cta_title') }}</h2>
                <a href="{{ Locale::route('contact') }}" class="mt-5 inline-flex btn btn-primary">{{ __('portfolio.nav.cta') }}</a>
            </div>
        </div>

        @if($recent->isNotEmpty())
            <div class="container-page mt-16">
                <h2 class="text-2xl font-bold mb-6">{{ __('portfolio.blog.more') }}</h2>
                <div class="grid sm:grid-cols-3 gap-5">
                    @foreach($recent as $r)
                        @php $rTopic = $r->getTranslation('topic', $l); @endphp
                        <a href="{{ Locale::route('blog.show', $r) }}" class="group card p-5 flex flex-col hover:-translate-y-1 transition-transform">
                            @if($rTopic)<span class="chip w-fit">{{ $rTopic }}</span>@endif
                            <h3 class="mt-3 font-display font-semibold text-[var(--color-ink)]">{{ $r->getTranslation('title', $l) }}</h3>
                            <span class="mt-3 text-sm text-[var(--color-brand-bright)] font-medium">{{ __('portfolio.blog.read') }} →</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>

</x-layout>
