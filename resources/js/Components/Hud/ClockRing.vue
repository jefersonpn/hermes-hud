<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';

const now = ref(new Date());
let timer = null;

onMounted(() => {
    timer = setInterval(() => (now.value = new Date()), 1000);
});
onBeforeUnmount(() => clearInterval(timer));

const pad = (n) => String(n).padStart(2, '0');
const hh = computed(() => pad(now.value.getHours()));
const mm = computed(() => pad(now.value.getMinutes()));
const ss = computed(() => pad(now.value.getSeconds()));
const dateStr = computed(() =>
    now.value.toLocaleDateString('it-IT', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
);
</script>

<template>
    <div class="relative flex flex-col items-center justify-center select-none">
        <svg viewBox="0 0 200 200" class="w-56 h-56">
            <g class="anim-spin-slow" style="transform-origin: 100px 100px">
                <circle cx="100" cy="100" r="92" fill="none" stroke="var(--color-hud-cyan)" stroke-width="1" opacity="0.5" />
                <circle cx="100" cy="100" r="92" fill="none" stroke="var(--color-hud-cyan)" stroke-width="2"
                    stroke-dasharray="6 6 22 8 4 4 60 4" />
            </g>
            <g class="anim-spin-med" style="transform-origin: 100px 100px">
                <circle cx="100" cy="100" r="78" fill="none" stroke="var(--color-hud-cyan-soft)" stroke-width="1" opacity="0.7"
                    stroke-dasharray="2 4" />
            </g>
            <g class="anim-spin-fast" style="transform-origin: 100px 100px">
                <circle cx="100" cy="100" r="65" fill="none" stroke="var(--color-hud-gold)" stroke-width="1" opacity="0.5"
                    stroke-dasharray="1 6" />
            </g>
            <circle cx="100" cy="100" r="55" fill="rgba(0,8,20,0.6)" stroke="var(--color-hud-dim)" stroke-width="1" />
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <div class="font-sans text-4xl tracking-widest hud-glow anim-pulse">
                {{ hh }}<span class="opacity-60">:</span>{{ mm }}<span class="opacity-50 text-2xl">:{{ ss }}</span>
            </div>
            <div class="font-mono text-xs uppercase mt-1 opacity-80">{{ dateStr }}</div>
        </div>
    </div>
</template>
