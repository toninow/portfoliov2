@php
    use App\Support\Locale;
    $l = app()->getLocale();
@endphp

<x-layout :title="__('portfolio.contact.title')" :description="__('portfolio.contact.lead')">
    <section class="section">
        <div class="container-page grid lg:grid-cols-[1fr_1.25fr] gap-10 lg:gap-14 items-start">
            <div data-reveal>
                <p class="eyebrow">{{ __('portfolio.nav.contact') }}</p>
                <h1 class="mt-2 text-4xl sm:text-5xl font-bold text-balance">{{ __('portfolio.contact.title') }}</h1>
                <p class="mt-5 text-lg text-[var(--color-muted)] max-w-md">{{ __('portfolio.contact.lead') }}</p>

                @if($profile->avatar_path)
                    <div class="mt-8 flex items-center gap-4">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($profile->avatar_path) }}"
                             alt="{{ $profile->name }}" width="56" height="56"
                             class="w-14 h-14 rounded-full object-cover border border-[var(--color-line)]" loading="lazy">
                        <div class="text-sm">
                            <p class="font-semibold text-[var(--color-ink)]">{{ $profile->name }}</p>
                            <p class="text-[var(--color-muted)]">{{ $profile->getTranslation('availability', $l) ?: __('portfolio.availability.available') }}</p>
                        </div>
                    </div>
                @endif

                <div class="mt-8 space-y-3 text-sm">
                    @if($profile->email)
                        <a href="mailto:{{ $profile->email }}" class="flex items-center gap-3 text-[var(--color-muted)] hover:text-[var(--color-ink)] transition-colors">
                            <span class="chip">@</span>{{ $profile->email }}
                        </a>
                    @endif
                    @if($profile->whatsapp)
                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $profile->whatsapp) }}" target="_blank" rel="noopener" class="flex items-center gap-3 text-[var(--color-muted)] hover:text-[var(--color-ink)] transition-colors">
                            <span class="chip">WA</span>WhatsApp
                        </a>
                    @endif
                </div>

                <p class="mt-8 text-sm text-[var(--color-muted)]">{{ __('portfolio.contact.response_time') }}</p>
            </div>

            <div data-reveal style="--reveal-delay: 90ms">
                @if(session('contact_success'))
                    <div role="status" class="card p-6 border-l-4 contact-success" style="border-left-color: var(--color-positive);">
                        <p class="text-[var(--color-ink)] text-lg">{{ __('portfolio.contact.success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="card p-5 mb-5 border-l-4" style="border-left-color: var(--color-danger);">
                        <ul class="text-sm text-[var(--color-muted)] space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ Locale::route('contact.store') }}" class="card p-6 sm:p-8 space-y-6" data-contact-form>
                    @csrf
                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <p class="text-sm text-[var(--color-muted)]">{{ __('portfolio.contact.intro') }}</p>

                    <div>
                        <label for="name" class="block text-sm mb-1.5">{{ __('portfolio.contact.name') }}</label>
                        <input id="name" name="name" required autocomplete="name" placeholder="{{ __('portfolio.contact.name_placeholder') }}" value="{{ old('name') }}" class="input-field">
                    </div>

                    <div>
                        <label for="email" class="block text-sm mb-1.5">{{ __('portfolio.contact.email') }}</label>
                        <input id="email" type="email" name="email" required autocomplete="email" placeholder="{{ __('portfolio.contact.email_placeholder') }}" value="{{ old('email') }}" class="input-field">
                    </div>

                    <div>
                        <label for="message" class="block text-sm mb-1.5">{{ __('portfolio.contact.message') }}</label>
                        <textarea id="message" name="message" rows="5" required placeholder="{{ __('portfolio.contact.message_placeholder') }}" class="input-field">{{ old('message') }}</textarea>
                    </div>

                    <details class="contact-more">
                        <summary class="text-sm text-[var(--color-muted)] cursor-pointer select-none hover:text-[var(--color-ink)] transition-colors">{{ __('portfolio.contact.more_details') }}</summary>
                        <div class="mt-4 grid sm:grid-cols-2 gap-5">
                            <div>
                                <label for="company" class="block text-sm mb-1.5">{{ __('portfolio.contact.company') }}</label>
                                <input id="company" name="company" autocomplete="organization" value="{{ old('company') }}" class="input-field">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm mb-1.5">{{ __('portfolio.contact.phone') }}</label>
                                <input id="phone" name="phone" autocomplete="tel" value="{{ old('phone') }}" class="input-field">
                            </div>
                        </div>
                    </details>

                    <label class="flex items-start gap-3 text-sm text-[var(--color-muted)]">
                        <input type="checkbox" name="consent" value="1" required class="mt-1 w-5 h-5 rounded border-[var(--color-line)]">
                        <span>{{ __('portfolio.contact.consent') }}</span>
                    </label>

                    <div>
                        <button type="submit" class="btn btn-primary w-full sm:w-auto" data-submit data-sending="{{ __('portfolio.contact.sending') }}">{{ __('portfolio.contact.send') }}</button>
                        <p class="mt-3 text-xs text-[var(--color-muted)]">{{ __('portfolio.contact.privacy') }}</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layout>
