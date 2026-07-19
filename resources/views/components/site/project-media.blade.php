@props([
    'project',
    'gallery',
])

@php
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $cover = $project->main_image_path ? Storage::url($project->main_image_path) : null;
    $video = $project->demo_video_path ? Storage::url($project->demo_video_path) : null;
    $items = $gallery->values();
    $hasMedia = $cover || $video || $items->isNotEmpty();
@endphp

@if($hasMedia)
    <section class="project-media" data-project-media aria-labelledby="project-media-title">
        <div class="flex items-end justify-between gap-4 mb-5">
            <h2 id="project-media-title" class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.evidence') }}</h2>
            @if($items->isNotEmpty())
                <p class="text-sm text-[var(--color-muted)] font-mono">{{ trans_choice('portfolio.projects.shots_count', $items->count(), ['count' => $items->count()]) }}</p>
            @endif
        </div>

        @if($video)
            <figure class="project-media__video card overflow-hidden mb-5">
                <video
                    class="w-full aspect-video bg-black"
                    controls
                    playsinline
                    preload="metadata"
                    @if($cover) poster="{{ $cover }}" @endif
                >
                    <source src="{{ $video }}" type="video/mp4">
                    {{ __('portfolio.projects.video_fallback') }}
                </video>
                <figcaption class="p-3 text-sm text-[var(--color-muted)]">{{ __('portfolio.projects.demo_video') }}</figcaption>
            </figure>
        @elseif($cover)
            <figure class="project-media__cover mb-5">
                <button type="button"
                        class="project-media__trigger block w-full text-left"
                        data-lightbox-open
                        data-lightbox-src="{{ $cover }}"
                        data-lightbox-alt="{{ $project->translated('name') }}"
                        data-lightbox-caption="{{ $project->translated('name') }}">
                    <img src="{{ $cover }}"
                         alt="{{ $project->translated('name') }}"
                         width="1200" height="720" loading="eager"
                         class="w-full rounded-2xl border border-[var(--color-line)]">
                </button>
            </figure>
        @endif

        @if($items->isNotEmpty())
            <ul class="project-media__grid">
                @foreach($items as $index => $image)
                    @php
                        $src = Storage::url($image->path);
                        $alt = $image->getTranslation('alt', $l) ?: $project->translated('name');
                        $caption = $image->getTranslation('caption', $l);
                    @endphp
                    <li>
                        <figure class="project-media__item card overflow-hidden">
                            <button type="button"
                                    class="project-media__trigger"
                                    data-lightbox-open
                                    data-lightbox-index="{{ $index }}"
                                    data-lightbox-src="{{ $src }}"
                                    data-lightbox-alt="{{ $alt }}"
                                    data-lightbox-caption="{{ $caption }}">
                                <img src="{{ $src }}"
                                     alt="{{ $alt }}"
                                     width="800" height="500" loading="lazy" decoding="async"
                                     class="w-full h-full object-cover aspect-[16/10]">
                            </button>
                            @if($caption)
                                <figcaption class="p-3 text-sm text-[var(--color-muted)]">{{ $caption }}</figcaption>
                            @endif
                        </figure>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <dialog class="project-lightbox" data-lightbox aria-label="{{ __('portfolio.projects.gallery') }}">
        <div class="project-lightbox__panel">
            <button type="button" class="project-lightbox__close" data-lightbox-close aria-label="{{ __('portfolio.projects.close_gallery') }}">×</button>
            <button type="button" class="project-lightbox__nav is-prev" data-lightbox-prev aria-label="{{ __('portfolio.projects.previous') }}">‹</button>
            <figure class="project-lightbox__figure">
                <img src="" alt="" data-lightbox-image width="1400" height="900">
                <figcaption class="project-lightbox__caption" data-lightbox-caption-el></figcaption>
            </figure>
            <button type="button" class="project-lightbox__nav is-next" data-lightbox-next aria-label="{{ __('portfolio.projects.next') }}">›</button>
        </div>
    </dialog>
@endif
