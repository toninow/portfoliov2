{{-- Impactful, on-brand header injected before the Filament login form. --}}
<div class="ab-login-brand">
    <span class="ab-login-monogram" aria-hidden="true">AB</span>
    <p class="ab-login-tagline">Sistemas · Automatización · Integraciones</p>
</div>

<style>
    /* Scoped to the auth pages (this hook only renders there). */
    .fi-simple-layout {
        background:
            radial-gradient(60% 60% at 50% 0%, rgba(59, 130, 246, 0.18), transparent 60%),
            radial-gradient(50% 50% at 100% 100%, rgba(34, 211, 238, 0.12), transparent 60%),
            linear-gradient(rgba(148, 163, 184, 0.05) 1px, transparent 1px) 0 0 / 28px 28px,
            linear-gradient(90deg, rgba(148, 163, 184, 0.05) 1px, transparent 1px) 0 0 / 28px 28px,
            #07111f !important;
    }

    .fi-simple-main {
        border: 1px solid rgba(96, 165, 250, 0.22) !important;
        box-shadow: 0 0 0 1px rgba(96, 165, 250, 0.12), 0 30px 80px -30px rgba(59, 130, 246, 0.45) !important;
        background: rgba(13, 27, 42, 0.85) !important;
        backdrop-filter: blur(8px);
    }

    /* Readable text on the dark login card (Filament defaults assume a light card). */
    .fi-simple-main :is(h1, h2, .fi-simple-header-heading) {
        color: #f8fafc !important;
    }

    .fi-simple-main .fi-simple-header-subheading {
        color: #b8c4d4 !important;
    }

    .fi-simple-main :is(label, .fi-fo-field-wrp-label, .fi-fo-field-wrp-label *) {
        color: #e2e8f0 !important;
    }

    .fi-simple-main a,
    .fi-simple-main .fi-link {
        color: #60a5fa !important;
    }

    /* Brand logo text (this stylesheet only loads on the auth pages). */
    .fi-logo {
        color: #f8fafc !important;
    }

    .ab-login-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
    }

    .ab-login-monogram {
        display: grid;
        place-items: center;
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 0.9rem;
        font-family: 'Sora', ui-sans-serif, system-ui, sans-serif;
        font-weight: 700;
        font-size: 1.25rem;
        letter-spacing: 0.02em;
        color: #fff;
        background: linear-gradient(135deg, #3b82f6, #22d3ee);
        box-shadow: 0 10px 30px -10px rgba(34, 211, 238, 0.7);
    }

    .ab-login-tagline {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #60a5fa;
        text-align: center;
    }
</style>
