@props([
    'project',
    'gallery',
])

@php
    use Illuminate\Support\Facades\Storage;
    $l = app()->getLocale();
    $cover = $project->main_image_path ? Storage::url($project->main_image_path) : null;
    $video = $project->demo_video_path ? Storage::url($project->demo_video_path) : null;

    $slides = collect();

    if ($video) {
        $slides->push([
            'type' => 'video',
            'src' => $video,
            'poster' => $cover,
            'alt' => $project->translated('name'),
            'caption' => __('portfolio.projects.demo_video'),
            'thumb' => $cover,
        ]);
    }

    foreach ($gallery->values() as $image) {
        $src = Storage::url($image->path);
        $alt = $image->getTranslation('alt', $l) ?: $project->translated('name');
        $caption = $image->getTranslation('caption', $l) ?: $alt;
        $slides->push([
            'type' => 'image',
            'src' => $src,
            'poster' => null,
            'alt' => $alt,
            'caption' => $caption,
            'thumb' => $src,
        ]);
    }

    if ($slides->isEmpty() && $cover) {
        $slides->push([
            'type' => 'image',
            'src' => $cover,
            'poster' => null,
            'alt' => $project->translated('name'),
            'caption' => $project->translated('name'),
            'thumb' => $cover,
        ]);
    }

    $total = $slides->count();
    $imageCount = $slides->where('type', 'image')->count();
    $hasVideo = (bool) $video;
@endphp

@if($total > 0)
    <section class="project-media" data-project-media aria-labelledby="project-media-title">
        <div class="project-media__head">
            <h2 id="project-media-title" class="text-xl sm:text-2xl font-bold">{{ __('portfolio.projects.evidence') }}</h2>
            <p class="project-media__meta font-mono">
                @if($hasVideo && $imageCount > 0)
                    {{ __('portfolio.projects.media_mixed', ['images' => $imageCount]) }}
                @elseif($hasVideo)
                    {{ __('portfolio.projects.demo_video') }}
                @else
                    {{ trans_choice('portfolio.projects.shots_count', $imageCount, ['count' => $imageCount]) }}
                @endif
            </p>
        </div>

        <div class="project-media__gallery card overflow-hidden" data-gallery>
            <div class="project-media__stage" data-gallery-stage>
                @foreach($slides as $index => $slide)
                    <figure
                        class="project-media__slide"
                        data-gallery-slide
                        data-slide-type="{{ $slide['type'] }}"
                        data-slide-index="{{ $index }}"
                        @if($index !== 0) hidden @endif
                    >
                        @if($slide['type'] === 'video')
                            <video
                                class="project-media__video"
                                controls
                                playsinline
                                preload="metadata"
                                @if($slide['poster']) poster="{{ $slide['poster'] }}" @endif
                                data-gallery-video
                            >
                                <source src="{{ $slide['src'] }}" type="video/mp4">
                                {{ __('portfolio.projects.video_fallback') }}
                            </video>
                        @else
                            <button type="button"
                                    class="project-media__trigger"
                                    data-lightbox-open
                                    data-lightbox-src="{{ $slide['src'] }}"
                                    data-lightbox-alt="{{ $slide['alt'] }}"
                                    data-lightbox-caption="{{ $slide['caption'] }}">
                                <img src="{{ $slide['src'] }}"
                                     alt="{{ $slide['alt'] }}"
                                     width="1200" height="720"
                                     loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                     decoding="async"
                                     class="project-media__image">
                            </button>
                        @endif
                        <figcaption class="project-media__caption" data-gallery-caption>
                            <span>{{ $slide['caption'] }}</span>
                            <span class="project-media__counter" data-gallery-counter>{{ $index + 1 }} / {{ $total }}</span>
                        </figcaption>
                    </figure>
                @endforeach

                @if($total > 1)
                    <button type="button" class="project-media__nav is-prev" data-gallery-prev aria-label="{{ __('portfolio.projects.previous') }}">‹</button>
                    <button type="button" class="project-media__nav is-next" data-gallery-next aria-label="{{ __('portfolio.projects.next') }}">›</button>
                @endif
            </div>

            @if($total > 1)
                <div class="project-media__thumbs" data-gallery-thumbs role="tablist" aria-label="{{ __('portfolio.projects.gallery') }}">
                    @foreach($slides as $index => $slide)
                        <button type="button"
                                class="project-media__thumb{{ $index === 0 ? ' is-active' : '' }}"
                                data-gallery-thumb
                                data-slide-index="{{ $index }}"
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="{{ $slide['caption'] }}">
                            @if($slide['thumb'])
                                <img src="{{ $slide['thumb'] }}" alt="" width="160" height="100" loading="lazy" decoding="async">
                            @else
                                <span class="project-media__thumb-fallback" aria-hidden="true"></span>
                            @endif
                            @if($slide['type'] === 'video')
                                <span class="project-media__thumb-play" aria-hidden="true">▶</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
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
