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

// Contact form: gentle submit feedback so it feels responsive and human.
(function initContactForm() {
    const form = document.querySelector('[data-contact-form]');
    if (!form) return;
    form.addEventListener('submit', () => {
        const btn = form.querySelector('[data-submit]');
        if (!btn) return;
        btn.dataset.label = btn.textContent;
        btn.textContent = btn.dataset.sending || btn.textContent;
        btn.disabled = true;
        btn.style.opacity = '0.75';
    });
})();
