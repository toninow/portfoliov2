<x-filament-panels::page>
    <div
        x-data="{
            device: 'desktop',
            widths: {{ \Illuminate\Support\Js::from(collect($devices)->map(fn ($d) => $d['width'])) }},
            locale: 'es',
            page: '/',
            get src() {
                const prefix = this.locale === 'en' ? '/en' : '';
                const path = this.page === '/' ? '' : this.page;
                return prefix + path;
            },
            reload() { const f = this.$refs.frame; f.src = f.src; },
        }"
        class="space-y-4"
    >
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex flex-wrap gap-1 rounded-lg border border-gray-200 dark:border-white/10 p-1">
                @foreach($devices as $key => $device)
                    <button type="button" x-on:click="device = '{{ $key }}'"
                            :class="device === '{{ $key }}' ? 'bg-primary-600 text-white' : 'text-gray-500'"
                            class="px-3 py-1.5 text-sm rounded-md">{{ $device['label'] }}</button>
                @endforeach
            </div>

            <select x-model="page" class="rounded-lg border-gray-300 dark:bg-gray-800 dark:border-white/10 text-sm">
                @foreach($pages as $path => $label)
                    <option value="{{ $path }}">{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-1 rounded-lg border border-gray-200 dark:border-white/10 p-1">
                <button type="button" x-on:click="locale = 'es'" :class="locale === 'es' ? 'bg-primary-600 text-white' : 'text-gray-500'" class="px-3 py-1.5 text-sm rounded-md">ES</button>
                <button type="button" x-on:click="locale = 'en'" :class="locale === 'en' ? 'bg-primary-600 text-white' : 'text-gray-500'" class="px-3 py-1.5 text-sm rounded-md">EN</button>
            </div>

            <button type="button" x-on:click="reload()" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-white/10">Refrescar</button>
            <a :href="src" target="_blank" class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 dark:border-white/10">Abrir en pestaña</a>
        </div>

        <div class="w-full overflow-x-auto rounded-xl bg-gray-100 dark:bg-gray-900 p-4">
            <div class="mx-auto rounded-xl border border-gray-300 dark:border-white/10 bg-white shadow-lg transition-all"
                 :style="`width: ${widths[device]}px; max-width: 100%;`">
                <iframe x-ref="frame" :src="src" title="Vista previa del sitio"
                        class="w-full rounded-xl" style="height: 75vh;" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</x-filament-panels::page>
