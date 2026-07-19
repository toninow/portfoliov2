@php
    use App\Support\Locale;
    $l = app()->getLocale();
    $sent = session('contact_success');
    $failed = session('contact_error');
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

            <div>
                <div id="estado-contacto" tabindex="-1" class="outline-none" data-contact-status
                     data-dismiss-label="{{ __('portfolio.contact.dismiss') }}"
                     @unless($sent || $failed || $errors->any()) hidden @endunless>
                    @if($sent)
                        <div role="status" aria-live="polite" class="contact-feedback contact-feedback--success mb-6">
                            <p class="font-display text-xl font-semibold text-[var(--color-ink)]">{{ __('portfolio.contact.success_title') }}</p>
                            <p class="mt-2 text-[var(--color-muted)]">{{ __('portfolio.contact.success') }}</p>
                            <button type="button" class="mt-4 btn btn-ghost" data-contact-dismiss>{{ __('portfolio.contact.dismiss') }}</button>
                        </div>
                    @endif

                    @if($failed)
                        <div role="alert" aria-live="assertive" class="contact-feedback contact-feedback--error mb-5">
                            <p class="font-semibold text-[var(--color-ink)]">{{ __('portfolio.contact.error_title') }}</p>
                            <p class="mt-1 text-sm text-[var(--color-muted)]">{{ session('contact_error_message') ?: __('portfolio.contact.error') }}</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div role="alert" aria-live="assertive" class="contact-feedback contact-feedback--error mb-5">
                            <p class="font-semibold text-[var(--color-ink)]">{{ __('portfolio.contact.validation_title') }}</p>
                            <ul class="mt-2 text-sm text-[var(--color-muted)] space-y-1 list-disc pl-5">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ Locale::route('contact.store') }}" class="card p-6 sm:p-8 space-y-6" data-contact-form novalidate
                      data-validation-title="{{ __('portfolio.contact.validation_title') }}"
                      data-error-title="{{ __('portfolio.contact.error_title') }}"
                      data-network-error="{{ __('portfolio.contact.error') }}"
                      @if($sent) hidden @endif>
                    @csrf
                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <p class="text-sm text-[var(--color-muted)]">{{ __('portfolio.contact.intro') }}</p>

                    <div>
                        <label for="name" class="block text-sm mb-1.5">{{ __('portfolio.contact.name') }}</label>
                        <input id="name" name="name" required autocomplete="name" placeholder="{{ __('portfolio.contact.name_placeholder') }}" value="{{ old('name') }}" class="input-field @error('name') border-[var(--color-danger)] @enderror" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                        @error('name')<p class="mt-1 text-sm text-[var(--color-danger)]" data-field-error="name">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm mb-1.5">{{ __('portfolio.contact.email') }}</label>
                        <input id="email" type="email" name="email" required autocomplete="email" placeholder="{{ __('portfolio.contact.email_placeholder') }}" value="{{ old('email') }}" class="input-field @error('email') border-[var(--color-danger)] @enderror" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
                        @error('email')<p class="mt-1 text-sm text-[var(--color-danger)]" data-field-error="email">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm mb-1.5">{{ __('portfolio.contact.message') }}</label>
                        <textarea id="message" name="message" rows="5" required placeholder="{{ __('portfolio.contact.message_placeholder') }}" class="input-field @error('message') border-[var(--color-danger)] @enderror" aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-sm text-[var(--color-danger)]" data-field-error="message">{{ $message }}</p>@enderror
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
                        <input type="checkbox" name="consent" value="1" required class="mt-1 w-5 h-5 rounded border-[var(--color-line)]" @checked(old('consent'))>
                        <span>{{ __('portfolio.contact.consent') }}</span>
                    </label>
                    @error('consent')<p class="text-sm text-[var(--color-danger)]" data-field-error="consent">{{ $message }}</p>@enderror

                    <div>
                        <button type="submit" class="btn btn-primary w-full sm:w-auto" data-submit data-sending="{{ __('portfolio.contact.sending') }}">{{ __('portfolio.contact.send') }}</button>
                        <p class="mt-3 text-xs text-[var(--color-muted)]">{{ __('portfolio.contact.privacy') }}</p>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-layout>
