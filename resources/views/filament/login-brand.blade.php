{{-- Impactful, on-brand header + animated Snake background for the login. --}}
<div class="ab-login-brand">
    <span class="ab-login-monogram" aria-hidden="true">AB</span>
    <p class="ab-login-tagline">Panel de administración</p>
</div>

<style>
    /* The canvas paints the whole background, so let the layout be transparent
       and sit above the game. */
    .fi-simple-layout {
        background: transparent !important;
        position: relative;
        z-index: 1;
    }

    body,
    .fi-body {
        background: #07111f !important;
    }

    #ab-snake-bg {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    .fi-simple-main {
        border: 1px solid rgba(96, 165, 250, 0.22) !important;
        box-shadow: 0 0 0 1px rgba(96, 165, 250, 0.12), 0 30px 80px -30px rgba(59, 130, 246, 0.55) !important;
        background: rgba(13, 27, 42, 0.78) !important;
        backdrop-filter: blur(10px);
    }

    /* Readable text on the dark login card. */
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

    .fi-logo {
        color: #f8fafc !important;
    }

    /* Simpler form: hide the "remember me" checkbox row (CSS + JS fallback). */
    .fi-simple-main .fi-fo-field-wrp:has(input[type="checkbox"]),
    .fi-simple-main .fi-fieldset:has(input[type="checkbox"]),
    .fi-simple-main [class*="field"]:has(> input.fi-checkbox-input),
    .ab-hide {
        display: none !important;
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

<script>
(function () {
    if (window.__abSnakeStarted) return;
    window.__abSnakeStarted = true;

    // Hide the "remember me" row reliably, even after Livewire re-renders.
    var hideRemember = function () {
        var cb = document.getElementById('form.remember');
        if (!cb) return;
        var row = cb.closest('.fi-fo-field-wrp, .fi-fieldset, .fi-fo-checkbox, label') || cb.parentElement;
        if (row) row.classList.add('ab-hide');
    };
    hideRemember();
    [200, 600, 1500].forEach(function (t) { setTimeout(hideRemember, t); });

    var start = function () {
        if (document.getElementById('ab-snake-bg')) return;

        var canvas = document.createElement('canvas');
        canvas.id = 'ab-snake-bg';
        document.body.appendChild(canvas);
        var ctx = canvas.getContext('2d');

        var CELL = 26;
        var cols = 0, rows = 0, dpr = Math.min(window.devicePixelRatio || 1, 2);

        function resize() {
            canvas.width = Math.floor(window.innerWidth * dpr);
            canvas.height = Math.floor(window.innerHeight * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            cols = Math.floor(window.innerWidth / CELL);
            rows = Math.floor(window.innerHeight / CELL);
        }
        resize();
        window.addEventListener('resize', resize);

        var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var snake, dir, food;

        function randCell() {
            return { x: Math.floor(Math.random() * cols), y: Math.floor(Math.random() * rows) };
        }
        function placeFood() {
            do { food = randCell(); } while (snake.some(function (s) { return s.x === food.x && s.y === food.y; }));
        }
        function reset() {
            var cx = Math.floor(cols / 2), cy = Math.floor(rows / 2);
            snake = [{ x: cx, y: cy }, { x: cx - 1, y: cy }, { x: cx - 2, y: cy }, { x: cx - 3, y: cy }];
            dir = { x: 1, y: 0 };
            placeFood();
        }
        reset();

        function safe(nx, ny, ignoreTail) {
            if (nx < 0 || ny < 0 || nx >= cols || ny >= rows) return false;
            for (var i = 0; i < snake.length - (ignoreTail ? 1 : 0); i++) {
                if (snake[i].x === nx && snake[i].y === ny) return false;
            }
            return true;
        }

        function chooseDir() {
            var head = snake[0];
            var opts = [{ x: 1, y: 0 }, { x: -1, y: 0 }, { x: 0, y: 1 }, { x: 0, y: -1 }];
            // No reversing.
            opts = opts.filter(function (d) { return !(d.x === -dir.x && d.y === -dir.y); });
            // Greedy: sort by resulting Manhattan distance to food.
            opts.sort(function (a, b) {
                var da = Math.abs(head.x + a.x - food.x) + Math.abs(head.y + a.y - food.y);
                var db = Math.abs(head.x + b.x - food.x) + Math.abs(head.y + b.y - food.y);
                return da - db;
            });
            for (var i = 0; i < opts.length; i++) {
                if (safe(head.x + opts[i].x, head.y + opts[i].y, true)) return opts[i];
            }
            for (var j = 0; j < opts.length; j++) {
                if (safe(head.x + opts[j].x, head.y + opts[j].y, false)) return opts[j];
            }
            return null;
        }

        function step() {
            var nd = chooseDir();
            if (!nd) { reset(); return; }
            dir = nd;
            var head = { x: snake[0].x + dir.x, y: snake[0].y + dir.y };
            snake.unshift(head);
            if (head.x === food.x && head.y === food.y) {
                placeFood();
                if (snake.length > 60) snake.pop(); // keep it from filling the screen
            } else {
                snake.pop();
            }
        }

        function roundRect(x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.arcTo(x + w, y, x + w, y + h, r);
            ctx.arcTo(x + w, y + h, x, y + h, r);
            ctx.arcTo(x, y + h, x, y, r);
            ctx.arcTo(x, y, x + w, y, r);
            ctx.closePath();
        }

        function draw() {
            var W = window.innerWidth, H = window.innerHeight;
            ctx.clearRect(0, 0, W, H);

            // Base + glows.
            ctx.fillStyle = '#07111f';
            ctx.fillRect(0, 0, W, H);
            var g1 = ctx.createRadialGradient(W * 0.5, 0, 0, W * 0.5, 0, H * 0.7);
            g1.addColorStop(0, 'rgba(59,130,246,0.16)');
            g1.addColorStop(1, 'rgba(59,130,246,0)');
            ctx.fillStyle = g1;
            ctx.fillRect(0, 0, W, H);

            // Grid.
            ctx.strokeStyle = 'rgba(148,163,184,0.06)';
            ctx.lineWidth = 1;
            for (var x = 0; x <= cols; x++) { ctx.beginPath(); ctx.moveTo(x * CELL, 0); ctx.lineTo(x * CELL, rows * CELL); ctx.stroke(); }
            for (var y = 0; y <= rows; y++) { ctx.beginPath(); ctx.moveTo(0, y * CELL); ctx.lineTo(cols * CELL, y * CELL); ctx.stroke(); }

            // Food.
            ctx.save();
            ctx.shadowColor = 'rgba(96,165,250,0.9)';
            ctx.shadowBlur = 14;
            ctx.fillStyle = '#60a5fa';
            ctx.beginPath();
            ctx.arc(food.x * CELL + CELL / 2, food.y * CELL + CELL / 2, CELL * 0.24, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();

            // Snake.
            for (var i = snake.length - 1; i >= 0; i--) {
                var s = snake[i];
                var t = 1 - i / snake.length;
                var pad = 3;
                ctx.save();
                if (i === 0) {
                    ctx.shadowColor = 'rgba(34,211,238,0.9)';
                    ctx.shadowBlur = 16;
                    ctx.fillStyle = '#22d3ee';
                } else {
                    ctx.fillStyle = 'rgba(34,211,238,' + (0.28 + t * 0.5).toFixed(3) + ')';
                }
                roundRect(s.x * CELL + pad, s.y * CELL + pad, CELL - pad * 2, CELL - pad * 2, 6);
                ctx.fill();
                ctx.restore();
            }
        }

        if (reduce) { step(); step(); step(); draw(); return; }

        var last = 0, acc = 0, INTERVAL = 115;
        function loop(ts) {
            if (!last) last = ts;
            acc += ts - last;
            last = ts;
            if (document.hidden) { acc = 0; requestAnimationFrame(loop); return; }
            while (acc >= INTERVAL) { step(); acc -= INTERVAL; }
            draw();
            requestAnimationFrame(loop);
        }
        requestAnimationFrame(loop);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>
