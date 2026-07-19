// Lightweight interactions only. No heavy libraries.

// Mobile navigation toggle
document.addEventListener('click', (e) => {
    const toggle = e.target.closest('[data-nav-toggle]');
    if (toggle) {
        const menu = document.getElementById('mobile-menu');
        if (menu) {
            const open = menu.hasAttribute('hidden');
            if (open) {
                menu.removeAttribute('hidden');
                toggle.setAttribute('aria-expanded', 'true');
            } else {
                menu.setAttribute('hidden', '');
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // Close menu when clicking a link inside it
    if (e.target.closest('#mobile-menu a')) {
        const menu = document.getElementById('mobile-menu');
        menu?.setAttribute('hidden', '');
    }
});

// Sticky nav background on scroll
const nav = document.querySelector('[data-sticky-nav]');
if (nav) {
    const onScroll = () => {
        if (window.scrollY > 24) {
            nav.classList.add('is-scrolled');
        } else {
            nav.classList.remove('is-scrolled');
        }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Interactive systems map (minimap): hover/focus previews, click selects, keyboard accessible.
(function initSystemsMap() {
    const map = document.querySelector('[data-system-map]');
    if (!map) return;

    const nodes = Array.from(map.querySelectorAll('.map-node'));
    const edges = Array.from(map.querySelectorAll('.map-edge'));
    const empty = map.querySelector('[data-map-empty]');
    const body = map.querySelector('[data-map-body]');
    const elRole = map.querySelector('[data-map-role]');
    const elTitle = map.querySelector('[data-map-title]');
    const elDesc = map.querySelector('[data-map-desc]');
    const elTags = map.querySelector('[data-map-tags]');
    const hint = map.querySelector('[data-map-hint]');
    if (!nodes.length || !body) return;

    let selected = null;

    const edgesFor = (index) =>
        edges.filter((e) => {
            const [a, b] = e.dataset.edge.split('-').map(Number);
            return a === index || b === index;
        });

    function highlight(index) {
        map.classList.add('is-focused');
        const active = new Set(edgesFor(index));
        edges.forEach((e) => {
            e.classList.toggle('is-active', active.has(e));
            e.classList.toggle('is-dim', !active.has(e));
        });
        const neighbors = new Set((nodes[index].dataset.adj || '').split(',').filter((x) => x !== ''));
        nodes.forEach((n, i) => {
            n.classList.toggle('is-active', i === index);
            n.classList.toggle('is-neighbor', neighbors.has(String(i)));
            n.classList.toggle('is-dim', i !== index && !neighbors.has(String(i)));
        });
    }

    function clear() {
        map.classList.remove('is-focused');
        edges.forEach((e) => e.classList.remove('is-active', 'is-dim'));
        nodes.forEach((n) => n.classList.remove('is-active', 'is-neighbor', 'is-dim'));
    }

    function fill(node) {
        elRole.textContent = node.dataset.role || '';
        elTitle.textContent = node.dataset.label || '';
        elDesc.textContent = node.dataset.desc || '';
        elTags.innerHTML = '';
        (node.dataset.tags || '')
            .split('|')
            .filter(Boolean)
            .forEach((t) => {
                const s = document.createElement('span');
                s.className = 'chip';
                s.textContent = t;
                elTags.appendChild(s);
            });
        empty.hidden = true;
        body.hidden = false;
        // Re-trigger the swap animation each time content changes.
        body.classList.remove('is-in');
        void body.offsetWidth;
        body.classList.add('is-in');
    }

    const svg = map.querySelector('svg');
    const nodesGroup = map.querySelector('.system-map__nodes');

    // Expanding ring at the clicked node for a tactile, game-like feel.
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function ripple(node) {
        if (reduceMotion || !svg || !nodesGroup) return;
        const ring = node.querySelector('.map-node__ring');
        if (!ring) return;
        const cx = ring.getAttribute('cx');
        const cy = ring.getAttribute('cy');
        const c = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        c.setAttribute('cx', cx);
        c.setAttribute('cy', cy);
        c.setAttribute('r', '12');
        c.setAttribute('class', 'map-ripple');
        nodesGroup.appendChild(c);
        c.addEventListener('animationend', () => c.remove());
    }

    function preview(i) {
        highlight(i);
        fill(nodes[i]);
    }

    function restore() {
        if (selected !== null) {
            highlight(selected);
            fill(nodes[selected]);
        } else {
            clear();
            empty.hidden = false;
            body.hidden = true;
        }
    }

    function select(i) {
        selected = i;
        highlight(i);
        fill(nodes[i]);
        ripple(nodes[i]);
        nodes.forEach((n, idx) => n.setAttribute('aria-pressed', idx === i ? 'true' : 'false'));
        if (hint) hint.style.opacity = '0';
    }

    nodes.forEach((n, i) => {
        n.addEventListener('mouseenter', () => preview(i));
        n.addEventListener('mouseleave', restore);
        n.addEventListener('focus', () => preview(i));
        n.addEventListener('blur', restore);
        n.addEventListener('click', () => select(i));
        n.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                select(i);
            }
        });
    });

    map.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            selected = null;
            nodes.forEach((n) => n.setAttribute('aria-pressed', 'false'));
            if (hint) hint.style.opacity = '';
            restore();
        }
    });
})();

// Reveal-on-scroll (respects reduced motion)
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealSelector = '[data-reveal], [data-reveal-stagger]';
if (!prefersReduced && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );
    document.querySelectorAll(revealSelector).forEach((el) => observer.observe(el));
} else {
    document.querySelectorAll(revealSelector).forEach((el) => el.classList.add('is-visible'));
}

// Contact form: AJAX submit with inline feedback (no full page reload).
(function initContactForm() {
    const status = document.querySelector('[data-contact-status]');
    const form = document.querySelector('[data-contact-form]');
    if (!status || !form) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let hideTimer = null;
    const labels = {
        validation: form.dataset.validationTitle || 'Error',
        error: form.dataset.errorTitle || 'Error',
        network: form.dataset.networkError || 'Error',
    };

    function clearFieldErrors() {
        form.querySelectorAll('[data-field-error]').forEach((el) => el.remove());
        form.querySelectorAll('[aria-invalid="true"]').forEach((el) => el.setAttribute('aria-invalid', 'false'));
        form.querySelectorAll('.border-\\[var\\(--color-danger\\)\\], .is-invalid').forEach((el) => {
            el.classList.remove('is-invalid');
        });
    }

    function showStatus(kind, title, message, { autoHide = false } = {}) {
        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }

        const dismissLabel = status.dataset.dismissLabel || 'OK';
        status.hidden = false;
        status.innerHTML = `
            <div role="${kind === 'success' ? 'status' : 'alert'}" aria-live="${kind === 'success' ? 'polite' : 'assertive'}"
                 class="contact-feedback contact-feedback--${kind} mb-6 is-in">
                <p class="font-display text-xl font-semibold text-[var(--color-ink)]"></p>
                <p class="mt-2 text-[var(--color-muted)]"></p>
                <button type="button" class="mt-4 btn btn-ghost" data-contact-dismiss></button>
            </div>
        `;
        const box = status.firstElementChild;
        box.querySelector('p:nth-of-type(1)').textContent = title;
        box.querySelector('p:nth-of-type(2)').textContent = message;
        box.querySelector('[data-contact-dismiss]').textContent = dismissLabel;

        status.focus({ preventScroll: true });
        status.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'nearest' });

        if (autoHide) {
            hideTimer = setTimeout(() => hideStatus(), reduceMotion ? 4000 : 7000);
        }
    }

    function hideStatus() {
        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }
        const box = status.querySelector('.contact-feedback');
        if (box && !reduceMotion) {
            box.classList.add('is-out');
            box.addEventListener(
                'animationend',
                () => {
                    status.hidden = true;
                    status.innerHTML = '';
                },
                { once: true }
            );
            return;
        }
        status.hidden = true;
        status.innerHTML = '';
    }

    status.addEventListener('click', (e) => {
        if (e.target.closest('[data-contact-dismiss]')) {
            hideStatus();
            form.hidden = false;
        }
    });

    // Legacy redirect flash: allow dismiss and restore form.
    if (!status.hidden && status.querySelector('.contact-feedback--success')) {
        form.hidden = true;
        hideTimer = setTimeout(() => {
            hideStatus();
            form.hidden = false;
        }, reduceMotion ? 4000 : 7000);
        status.focus({ preventScroll: true });
        status.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'nearest' });
    } else if (!status.hidden) {
        status.focus({ preventScroll: true });
        status.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'nearest' });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = form.querySelector('[data-submit]');
        if (!btn || btn.disabled) return;

        clearFieldErrors();
        btn.dataset.label = btn.textContent;
        btn.textContent = btn.dataset.sending || btn.textContent;
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btn.style.opacity = '0.75';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 422) {
                const errors = data.errors || {};
                Object.entries(errors).forEach(([field, messages]) => {
                    const input = form.querySelector(`[name="${field}"]`);
                    const text = Array.isArray(messages) ? messages[0] : String(messages);
                    if (input) {
                        input.setAttribute('aria-invalid', 'true');
                        const p = document.createElement('p');
                        p.className = 'mt-1 text-sm text-[var(--color-danger)]';
                        p.dataset.fieldError = field;
                        p.textContent = text;
                        (input.closest('label') || input).insertAdjacentElement('afterend', p);
                    }
                });
                showStatus('error', labels.validation, Object.values(errors).flat()[0] || '');
                return;
            }

            if (!response.ok || data.ok === false) {
                showStatus('error', data.title || labels.error, data.message || labels.network);
                return;
            }

            form.reset();
            form.hidden = true;
            showStatus('success', data.title, data.message, { autoHide: true });
            // After auto-hide, bring the form back so the user can write again.
            const reopenDelay = reduceMotion ? 4200 : 7200;
            setTimeout(() => {
                form.hidden = false;
            }, reopenDelay);
        } catch {
            showStatus('error', labels.error, labels.network);
        } finally {
            btn.textContent = btn.dataset.label || btn.textContent;
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.style.opacity = '';
        }
    });
})();

// Contact form: show reason-specific optional fields.
(function initContactReason() {
    const select = document.querySelector('[data-contact-reason]');
    if (!select) return;

    const sync = () => {
        const reason = select.value;
        document.querySelectorAll('[data-reason-fields]').forEach((panel) => {
            const key = panel.getAttribute('data-reason-fields');
            const visible = key === 'job'
                ? reason === 'job'
                : key === 'project'
                    ? reason === 'project' || reason === 'consulting'
                    : false;
            panel.classList.toggle('hidden', !visible);
            panel.querySelectorAll('input, select, textarea').forEach((input) => {
                input.disabled = !visible;
            });
        });
    };

    select.addEventListener('change', sync);
    sync();
})();

// About page: expand/collapse additional technologies.
(function initTechMore() {
    const button = document.querySelector('[data-tech-more]');
    const panel = document.getElementById('tech-additional-panel');
    if (!button || !panel) return;

    const labelClosed = button.querySelector('[data-tech-more-label-closed]');
    const labelOpen = button.querySelector('[data-tech-more-label-open]');

    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        const next = !expanded;
        button.setAttribute('aria-expanded', next ? 'true' : 'false');
        panel.hidden = !next;
        if (labelClosed) labelClosed.hidden = next;
        if (labelOpen) labelOpen.hidden = !next;
    });
})();

// Related projects: 3-up carousel with infinite wrap + dots + autoplay.
(function initRelatedSlider() {
    const root = document.querySelector('[data-related-slider]');
    if (!root) return;

    const track = root.querySelector('[data-related-track]');
    const items = Array.from(root.querySelectorAll('[data-related-item]'));
    const dotsEl = root.querySelector('[data-related-dots]');
    const prevBtn = root.querySelector('[data-related-prev]');
    const nextBtn = root.querySelector('[data-related-next]');
    if (!track || items.length === 0 || !dotsEl) return;

    let page = 0;
    let perView = 3;
    let timer = 0;
    let paused = false;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const autoplayMs = (() => {
        const raw = Number.parseInt(root.getAttribute('data-autoplay') || '4000', 10);
        if (!Number.isFinite(raw) || raw <= 0 || reduceMotion) return 0;
        return raw;
    })();

    const readPerView = () => {
        const raw = getComputedStyle(track).getPropertyValue('--related-per-view').trim();
        const n = Number.parseInt(raw, 10);
        return Number.isFinite(n) && n > 0 ? n : 3;
    };

    const pageCount = () => Math.max(1, Math.ceil(items.length / perView));

    const stopAutoplay = () => {
        if (timer) {
            window.clearInterval(timer);
            timer = 0;
        }
    };

    const startAutoplay = () => {
        stopAutoplay();
        if (!autoplayMs || paused || pageCount() <= 1) return;
        timer = window.setInterval(() => goTo(page + 1, false), autoplayMs);
    };

    const renderDots = () => {
        const total = pageCount();
        dotsEl.hidden = total <= 1;
        dotsEl.innerHTML = '';
        for (let i = 0; i < total; i += 1) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'related-slider__dot' + (i === page ? ' is-active' : '');
            btn.setAttribute('role', 'tab');
            btn.setAttribute('aria-selected', i === page ? 'true' : 'false');
            btn.setAttribute('aria-label', `${i + 1} / ${total}`);
            btn.addEventListener('click', () => goTo(i));
            dotsEl.appendChild(btn);
        }
        prevBtn?.toggleAttribute('hidden', total <= 1);
        nextBtn?.toggleAttribute('hidden', total <= 1);
    };

    const apply = () => {
        const total = pageCount();
        page = ((page % total) + total) % total;
        const offset = page * perView;
        const styles = getComputedStyle(track);
        const gap = Number.parseFloat(styles.columnGap || styles.gap) || 0;
        const itemWidth = items[0].getBoundingClientRect().width;
        track.style.transform = `translateX(-${offset * (itemWidth + gap)}px)`;

        Array.from(dotsEl.children).forEach((dot, i) => {
            const active = i === page;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        items.forEach((item, i) => {
            const visible = i >= offset && i < offset + perView;
            item.toggleAttribute('aria-hidden', !visible);
            item.inert = !visible;
        });
    };

    const goTo = (next, restart = true) => {
        const total = pageCount();
        page = ((next % total) + total) % total;
        apply();
        if (restart) startAutoplay();
    };

    let resizeTimer = 0;
    const sync = () => {
        perView = readPerView();
        page = Math.min(page, pageCount() - 1);
        renderDots();
        apply();
        startAutoplay();
    };

    prevBtn?.addEventListener('click', () => goTo(page - 1));
    nextBtn?.addEventListener('click', () => goTo(page + 1));

    root.addEventListener('mouseenter', () => {
        paused = true;
        stopAutoplay();
    });
    root.addEventListener('mouseleave', () => {
        paused = false;
        startAutoplay();
    });
    root.addEventListener('focusin', () => {
        paused = true;
        stopAutoplay();
    });
    root.addEventListener('focusout', (event) => {
        if (root.contains(event.relatedTarget)) return;
        paused = false;
        startAutoplay();
    });

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopAutoplay();
        } else if (!paused) {
            startAutoplay();
        }
    });

    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(sync, 120);
    });

    sync();
})();

// Project case study: unified gallery stage + lightbox for images.
(function initProjectGallery() {
    const root = document.querySelector('[data-project-media]');
    if (!root) return;

    const gallery = root.querySelector('[data-gallery]');
    const slides = Array.from(root.querySelectorAll('[data-gallery-slide]'));
    const thumbs = Array.from(root.querySelectorAll('[data-gallery-thumb]'));
    const prevBtn = root.querySelector('[data-gallery-prev]');
    const nextBtn = root.querySelector('[data-gallery-next]');
    let index = 0;

    const pauseVideos = () => {
        root.querySelectorAll('[data-gallery-video]').forEach((video) => {
            if (!video.paused) video.pause();
        });
    };

    const show = (nextIndex) => {
        if (slides.length === 0) return;
        index = ((nextIndex % slides.length) + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            const active = i === index;
            slide.hidden = !active;
            if (!active) {
                slide.querySelectorAll('video').forEach((video) => {
                    if (!video.paused) video.pause();
                });
            }
        });
        thumbs.forEach((thumb, i) => {
            const active = i === index;
            thumb.classList.toggle('is-active', active);
            thumb.setAttribute('aria-selected', active ? 'true' : 'false');
            if (active) {
                thumb.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });
            }
        });
    };

    prevBtn?.addEventListener('click', () => show(index - 1));
    nextBtn?.addEventListener('click', () => show(index + 1));
    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const raw = Number.parseInt(thumb.getAttribute('data-slide-index') || '0', 10);
            show(Number.isFinite(raw) ? raw : 0);
        });
    });

    gallery?.addEventListener('keydown', (event) => {
        if (event.target.closest('video, dialog')) return;
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            show(index - 1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            show(index + 1);
        }
    });

    const dialog = document.querySelector('[data-lightbox]');
    if (!dialog || typeof dialog.showModal !== 'function') return;

    const imageEl = dialog.querySelector('[data-lightbox-image]');
    const captionEl = dialog.querySelector('[data-lightbox-caption-el]');
    const lbPrev = dialog.querySelector('[data-lightbox-prev]');
    const lbNext = dialog.querySelector('[data-lightbox-next]');
    const closeBtn = dialog.querySelector('[data-lightbox-close]');
    const triggers = Array.from(root.querySelectorAll('[data-lightbox-open]'));
    if (!imageEl || triggers.length === 0) return;

    let lbIndex = 0;
    const items = triggers.map((el) => ({
        src: el.getAttribute('data-lightbox-src') || '',
        alt: el.getAttribute('data-lightbox-alt') || '',
        caption: el.getAttribute('data-lightbox-caption') || '',
    })).filter((item) => item.src);

    const renderLightbox = () => {
        const item = items[lbIndex];
        if (!item) return;
        imageEl.src = item.src;
        imageEl.alt = item.alt;
        if (captionEl) captionEl.textContent = item.caption || '';
        const multi = items.length > 1;
        if (lbPrev) lbPrev.hidden = !multi;
        if (lbNext) lbNext.hidden = !multi;
    };

    const openLightbox = (i) => {
        pauseVideos();
        lbIndex = ((i % items.length) + items.length) % items.length;
        renderLightbox();
        if (!dialog.open) dialog.showModal();
    };

    const stepLightbox = (delta) => {
        lbIndex = (lbIndex + delta + items.length) % items.length;
        renderLightbox();
    };

    triggers.forEach((el, i) => {
        el.addEventListener('click', () => openLightbox(i));
    });

    lbPrev?.addEventListener('click', () => stepLightbox(-1));
    lbNext?.addEventListener('click', () => stepLightbox(1));
    closeBtn?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener('keydown', (event) => {
        if (!dialog.open) return;
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            stepLightbox(-1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            stepLightbox(1);
        }
    });
    dialog.addEventListener('close', () => {
        imageEl.removeAttribute('src');
    });
})();
