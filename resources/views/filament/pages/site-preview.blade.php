<x-filament-panels::page>
    @php
        $deviceData = collect($this->devices)->map(fn ($d) => [
            'label' => $d['label'],
            'width' => $d['width'],
            'height' => $d['height'],
            'frame' => $d['frame'],
        ]);
    @endphp

    <div
        x-data="{
            devices: {{ \Illuminate\Support\Js::from($deviceData) }},
            origin: {{ \Illuminate\Support\Js::from(rtrim((string) config('app.url'), '/')) }},
            active: 'tablet',
            page: '/',
            locale: 'es',
            scale: 1,
            scaledWidth: 768,
            scaledHeight: 1024,
            get current() {
                return this.devices[this.active] || Object.values(this.devices)[0];
            },
            get frameClass() {
                return {
                    'is-desktop': this.current.frame === 'desktop',
                    'is-tablet': this.current.frame === 'tablet',
                    'is-mobile': this.current.frame === 'mobile',
                };
            },
            get frameSrc() {
                const prefix = this.locale === 'en' ? '/en' : '';
                const path = this.page === '/' ? '' : this.page;
                return this.origin + prefix + path;
            },
            get metaLabel() {
                return this.current.width + '×' + this.current.height + ' · ' + Math.round(this.scale * 100) + '%';
            },
            init() {
                this.$nextTick(() => {
                    this.fit();
                    this.refreshFrame();
                    if (window.ResizeObserver) {
                        new ResizeObserver(() => this.fit()).observe(this.$refs.stage);
                    } else {
                        window.addEventListener('resize', () => this.fit());
                    }
                });
            },
            setDevice(key) {
                this.active = key;
                this.$nextTick(() => this.fit());
            },
            setLocale(locale) {
                this.locale = locale;
                this.refreshFrame();
            },
            refreshFrame() {
                const frame = this.$refs.frame;
                if (!frame) return;
                frame.src = this.frameSrc;
            },
            fit() {
                const stage = this.$refs.stage;
                if (!stage) return;
                const padding = 48;
                const availableW = Math.max(240, stage.clientWidth - padding);
                const availableH = Math.max(360, stage.clientHeight - padding);
                const chromeY = this.current.frame === 'desktop' ? 0 : 44;
                const frameW = this.current.width + (this.current.frame === 'desktop' ? 0 : 28);
                const frameH = this.current.height + chromeY;
                const nextScale = Math.min(1, availableW / frameW, availableH / frameH);
                this.scale = Math.max(0.28, nextScale);
                this.scaledWidth = Math.ceil(frameW * this.scale);
                this.scaledHeight = Math.ceil(frameH * this.scale);
            },
        }"
        class="fi-site-preview space-y-4"
    >
        <div class="fi-site-preview__toolbar flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-white/10 dark:bg-gray-900 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="flex flex-wrap gap-1 rounded-lg border border-gray-200 p-1 dark:border-white/10" role="tablist" aria-label="Dispositivo">
                @foreach($this->devices as $key => $device)
                    <button
                        type="button"
                        role="tab"
                        x-on:click="setDevice('{{ $key }}')"
                        :aria-selected="active === '{{ $key }}'"
                        :class="active === '{{ $key }}'
                            ? 'bg-primary-600 text-white shadow-sm'
                            : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5'"
                        class="rounded-md px-3 py-1.5 text-sm font-medium transition"
                    >{{ $device['label'] }}</button>
                @endforeach
            </div>

            <select
                x-model="page"
                x-on:change="refreshFrame()"
                class="rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-gray-800"
                aria-label="Página"
            >
                @foreach($this->pages as $path => $label)
                    <option value="{{ $path }}">{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-1 rounded-lg border border-gray-200 p-1 dark:border-white/10">
                <button type="button" x-on:click="setLocale('es')" :class="locale === 'es' ? 'bg-primary-600 text-white' : 'text-gray-500'" class="rounded-md px-3 py-1.5 text-sm font-medium">ES</button>
                <button type="button" x-on:click="setLocale('en')" :class="locale === 'en' ? 'bg-primary-600 text-white' : 'text-gray-500'" class="rounded-md px-3 py-1.5 text-sm font-medium">EN</button>
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:ml-auto">
                <span class="font-mono text-xs text-gray-500 dark:text-gray-400" x-text="metaLabel"></span>
                <button type="button" x-on:click="refreshFrame()" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm dark:border-white/10">Refrescar</button>
                <a :href="frameSrc" target="_blank" rel="noopener" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm dark:border-white/10">Abrir</a>
            </div>
        </div>

        <div
            x-ref="stage"
            class="fi-site-preview__stage flex min-h-[72vh] items-start justify-center overflow-auto rounded-xl border border-gray-200 bg-gray-100 p-4 dark:border-white/10 dark:bg-gray-950 sm:p-8"
        >
            <div
                class="fi-site-preview__scaler origin-top"
                :style="'width:' + scaledWidth + 'px;height:' + scaledHeight + 'px;'"
            >
                <div
                    class="fi-site-preview__device relative origin-top"
                    :class="frameClass"
                    :style="'width:' + (current.width + (current.frame === 'desktop' ? 0 : 20)) + 'px;transform:scale(' + scale + ');transform-origin:top center;'"
                >
                    <div class="fi-site-preview__chrome" x-show="current.frame !== 'desktop'">
                        <span class="fi-site-preview__notch" x-show="current.frame === 'mobile'"></span>
                        <span class="fi-site-preview__speaker" x-show="current.frame === 'tablet'"></span>
                    </div>

                    <iframe
                        x-ref="frame"
                        title="Vista previa del sitio"
                        class="fi-site-preview__frame block bg-white"
                        :style="'width:' + current.width + 'px;height:' + current.height + 'px;'"
                        loading="eager"
                    ></iframe>

                    <div class="fi-site-preview__home" x-show="current.frame === 'mobile'"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fi-site-preview__device {
            margin-inline: auto;
            background: #0b1220;
            box-shadow:
                0 25px 60px rgb(0 0 0 / 0.28),
                0 0 0 1px rgb(15 23 42 / 0.2);
        }

        .fi-site-preview__device.is-desktop {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .fi-site-preview__device.is-tablet {
            border-radius: 1.35rem;
            padding: 18px 14px 22px;
            height: auto !important;
        }

        .fi-site-preview__device.is-mobile {
            border-radius: 2rem;
            padding: 14px 10px 18px;
            height: auto !important;
        }

        .fi-site-preview__chrome {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 14px;
            margin-bottom: 8px;
        }

        .fi-site-preview__notch {
            width: 96px;
            height: 18px;
            border-radius: 999px;
            background: #020617;
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.06);
        }

        .fi-site-preview__speaker {
            width: 64px;
            height: 6px;
            border-radius: 999px;
            background: #1f2937;
        }

        .fi-site-preview__frame {
            border: 0;
            border-radius: 0.65rem;
            background: #fff;
        }

        .fi-site-preview__device.is-mobile .fi-site-preview__frame {
            border-radius: 1.1rem;
        }

        .fi-site-preview__home {
            width: 96px;
            height: 4px;
            border-radius: 999px;
            background: rgb(255 255 255 / 0.35);
            margin: 10px auto 0;
        }
    </style>
</x-filament-panels::page>
