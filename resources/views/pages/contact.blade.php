@php
    use App\Support\Locale;
    $l = app()->getLocale();
@endphp

<x-layout :title="__('portfolio.contact.title')" :description="__('portfolio.contact.lead')">
    <section class="section">
        <div class="container-page grid lg:grid-cols-[1fr_1.3fr] gap-10">
            <div>
                <p class="eyebrow">{{ __('portfolio.nav.contact') }}</p>
                <h1 class="mt-2 text-4xl sm:text-5xl font-bold">{{ __('portfolio.contact.title') }}</h1>
                <p class="mt-4 text-lg text-[var(--color-muted)]">{{ __('portfolio.contact.lead') }}</p>

                <div class="mt-8 space-y-3 text-sm">
                    @if($profile->email)
                        <a href="mailto:{{ $profile->email }}" class="flex items-center gap-3 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                            <span class="chip">@</span>{{ $profile->email }}
                        </a>
                    @endif
                    @if($profile->whatsapp)
                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/[^0-9]/', '', $profile->whatsapp) }}" target="_blank" rel="noopener" class="flex items-center gap-3 text-[var(--color-muted)] hover:text-[var(--color-ink)]">
                            <span class="chip">WA</span>WhatsApp
                        </a>
                    @endif
                </div>

                <p class="mt-8 text-sm text-[var(--color-muted)]">{{ __('portfolio.contact.response_time') }}</p>
                <p class="mt-2 text-xs text-[var(--color-muted)]">{{ __('portfolio.contact.privacy') }}</p>
            </div>

            <div>
                @if(session('contact_success'))
                    <div role="status" class="card p-5 border-l-4" style="border-left-color: var(--color-positive);">
                        <p class="text-[var(--color-ink)]">{{ __('portfolio.contact.success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div role="alert" class="card p-5 mb-5 border-l-4" style="border-left-color: var(--color-danger);">
                        <ul class="text-sm text-[var(--color-muted)] space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ Locale::route('contact.store') }}" class="mt-2 card p-6 sm:p-8 space-y-5">
                    @csrf
                    {{-- Honeypot --}}
                    <div class="hidden" aria-hidden="true">
                        <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm mb-1.5">{{ __('portfolio.contact.name') }}</label>
                            <input id="name" name="name" required value="{{ old('name') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                        <div>
                            <label for="email" class="block text-sm mb-1.5">{{ __('portfolio.contact.email') }}</label>
                            <input id="email" type="email" name="email" required value="{{ old('email') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm mb-1.5">{{ __('portfolio.contact.phone') }} <span class="text-xs text-[var(--color-muted)]">({{ __('portfolio.contact.optional') }})</span></label>
                            <input id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                        <div>
                            <label for="company" class="block text-sm mb-1.5">{{ __('portfolio.contact.company') }} <span class="text-xs text-[var(--color-muted)]">({{ __('portfolio.contact.optional') }})</span></label>
                            <input id="company" name="company" value="{{ old('company') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                        <div>
                            <label for="country" class="block text-sm mb-1.5">{{ __('portfolio.contact.country') }} <span class="text-xs text-[var(--color-muted)]">({{ __('portfolio.contact.optional') }})</span></label>
                            <input id="country" name="country" value="{{ old('country') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                        <div>
                            <label for="need_type" class="block text-sm mb-1.5">{{ __('portfolio.contact.need_type') }} <span class="text-xs text-[var(--color-muted)]">({{ __('portfolio.contact.optional') }})</span></label>
                            <input id="need_type" name="need_type" value="{{ old('need_type') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm mb-1.5">{{ __('portfolio.contact.subject') }} <span class="text-xs text-[var(--color-muted)]">({{ __('portfolio.contact.optional') }})</span></label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">
                    </div>

                    <div>
                        <label for="message" class="block text-sm mb-1.5">{{ __('portfolio.contact.message') }}</label>
                        <textarea id="message" name="message" rows="5" required class="w-full rounded-lg bg-[var(--color-surface)] border border-[var(--color-line)] px-4 py-3 text-base">{{ old('message') }}</textarea>
                    </div>

                    <label class="flex items-start gap-3 text-sm text-[var(--color-muted)]">
                        <input type="checkbox" name="consent" value="1" required class="mt-1 w-5 h-5 rounded border-[var(--color-line)]">
                        <span>{{ __('portfolio.contact.consent') }}</span>
                    </label>

                    <button type="submit" class="btn btn-primary w-full sm:w-auto">{{ __('portfolio.contact.send') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-layout>
