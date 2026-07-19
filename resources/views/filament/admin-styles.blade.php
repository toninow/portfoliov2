<style>
    :root {
        --ab-ink: #e8eef7;
        --ab-muted: #93a4bd;
        --ab-line: rgba(96, 165, 250, 0.18);
        --ab-brand: #3b82f6;
        --ab-cyan: #22d3ee;
        --ab-sidebar: #07111f;
        --ab-sidebar-elevated: #0d1b2a;
    }

    /* Brand in topbar / sidebar header */
    .ab-admin-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        min-width: 0;
    }

    .ab-admin-brand__mark {
        display: grid;
        place-items: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.65rem;
        flex: none;
        font-family: ui-sans-serif, system-ui, sans-serif;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.02em;
        color: #fff;
        background: linear-gradient(135deg, var(--ab-brand), var(--ab-cyan));
        box-shadow: 0 8px 20px -10px rgba(34, 211, 238, 0.85);
    }

    .ab-admin-brand__text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        line-height: 1.15;
    }

    .ab-admin-brand__name {
        font-size: 0.88rem;
        font-weight: 700;
        color: inherit;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ab-admin-brand__meta {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--ab-cyan);
        opacity: 0.9;
    }

    /* Sidebar shell */
    .fi-sidebar {
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.22), transparent 42%),
            radial-gradient(circle at bottom right, rgba(34, 211, 238, 0.12), transparent 45%),
            linear-gradient(180deg, #0a1628 0%, var(--ab-sidebar) 55%, #050b14 100%) !important;
        border-inline-end: 1px solid var(--ab-line) !important;
        box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.03);
    }

    .fi-sidebar-header {
        border-bottom: 1px solid var(--ab-line) !important;
        background: rgba(7, 17, 31, 0.55) !important;
        backdrop-filter: blur(8px);
    }

    .fi-sidebar .fi-logo,
    .fi-sidebar .ab-admin-brand__name {
        color: var(--ab-ink) !important;
    }

    .fi-sidebar-nav {
        padding-block: 0.85rem !important;
    }

    /* Group labels */
    .fi-sidebar-group-label,
    .fi-sidebar .fi-sidebar-group > .fi-sidebar-group-button {
        color: var(--ab-muted) !important;
        font-size: 0.68rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.1em !important;
        text-transform: uppercase !important;
    }

    .fi-sidebar-group {
        margin-block: 0.55rem !important;
    }

    /* Nav items */
    .fi-sidebar-item-btn {
        border-radius: 0.7rem !important;
        color: #c6d0e0 !important;
        border: 1px solid transparent;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
    }

    .fi-sidebar-item-btn:hover {
        background: rgba(59, 130, 246, 0.12) !important;
        border-color: rgba(96, 165, 250, 0.18) !important;
        color: #f8fafc !important;
    }

    .fi-sidebar-item-btn .fi-sidebar-item-icon,
    .fi-sidebar-item-btn .fi-icon {
        color: #7dd3fc !important;
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
    .fi-sidebar-item-btn[aria-current="page"] {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.28), rgba(34, 211, 238, 0.16)) !important;
        border-color: rgba(96, 165, 250, 0.35) !important;
        color: #fff !important;
        box-shadow: 0 8px 24px -16px rgba(34, 211, 238, 0.9);
    }

    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn .fi-icon,
    .fi-sidebar-item-btn[aria-current="page"] .fi-icon {
        color: #67e8f9 !important;
    }

    .fi-sidebar-item-label {
        font-weight: 600 !important;
    }

    /* Collapsed / footer polish */
    .fi-sidebar-footer {
        border-top: 1px solid var(--ab-line) !important;
        background: rgba(5, 11, 20, 0.65) !important;
    }

    /* Main content slight contrast against brand sidebar */
    .fi-main {
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.05), transparent 28%),
            transparent;
    }

    /* Topbar brand alignment when sidebar is collapsed on desktop */
    .fi-topbar .ab-admin-brand__meta {
        display: none;
    }
</style>
