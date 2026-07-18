@php use App\Support\Locale; @endphp
<x-layout :title="__('portfolio.errors.404_title')" :indexable="false">
    <section class="section">
        <div class="container-page text-center max-w-xl mx-auto">
            <p class="font-mono text-6xl sm:text-8xl font-bold text-[var(--color-brand-bright)]">404</p>
            <h1 class="mt-4 text-2xl sm:text-3xl font-bold">{{ __('portfolio.errors.404_title') }}</h1>
            <p class="mt-3 text-[var(--color-muted)]">{{ __('portfolio.errors.404_body') }}</p>
            <a href="{{ Locale::route('home') }}" class="mt-8 inline-flex btn btn-primary">{{ __('portfolio.errors.back_home') }}</a>
        </div>
    </section>
</x-layout>
