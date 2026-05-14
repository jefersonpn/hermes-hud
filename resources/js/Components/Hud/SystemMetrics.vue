<script setup>
import { computed } from 'vue';

const props = defineProps({
    cpu: { type: Number, default: 0 },
    ram: { type: Number, default: 0 },
    cores: { type: Number, default: 0 },
});

const R = 44;
const CIRC = 2 * Math.PI * R;

const dash = (pct) => {
    const v = Math.min(100, Math.max(0, pct));
    return `${(v / 100) * CIRC} ${CIRC}`;
};

const cpuDash = computed(() => dash(props.cpu));
const ramDash = computed(() => dash(props.ram));
const cpuColor = computed(() => (props.cpu > 80 ? 'var(--color-hud-alert)' : 'var(--color-hud-cyan)'));
const ramColor = computed(() => (props.ram > 80 ? 'var(--color-hud-alert)' : 'var(--color-hud-gold)'));
</script>

<template>
    <div class="hud-panel hud-border rounded-md p-4 w-full">
        <div class="flex items-center justify-between mb-3">
            <div class="font-mono text-xs uppercase opacity-80 tracking-widest">[ System.Vitals ]</div>
            <div class="font-mono text-[10px] opacity-50">cores: {{ cores || '—' }}</div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col items-center">
                <svg viewBox="0 0 100 100" class="w-28 h-28">
                    <circle cx="50" cy="50" :r="R" fill="none" stroke="var(--color-hud-dimmer)" stroke-width="6" />
                    <circle cx="50" cy="50" :r="R" fill="none" :stroke="cpuColor" stroke-width="6" stroke-linecap="round"
                        :stroke-dasharray="cpuDash" transform="rotate(-90 50 50)"
                        style="filter: drop-shadow(0 0 6px var(--color-hud-cyan))" />
                    <text x="50" y="48" text-anchor="middle" class="font-sans" font-size="16" fill="var(--color-hud-cyan)">
                        {{ Math.round(cpu) }}%
                    </text>
                    <text x="50" y="62" text-anchor="middle" font-size="7" fill="var(--color-hud-dim)" letter-spacing="2">CPU</text>
                </svg>
            </div>
            <div class="flex flex-col items-center">
                <svg viewBox="0 0 100 100" class="w-28 h-28">
                    <circle cx="50" cy="50" :r="R" fill="none" stroke="var(--color-hud-dimmer)" stroke-width="6" />
                    <circle cx="50" cy="50" :r="R" fill="none" :stroke="ramColor" stroke-width="6" stroke-linecap="round"
                        :stroke-dasharray="ramDash" transform="rotate(-90 50 50)"
                        style="filter: drop-shadow(0 0 6px var(--color-hud-gold))" />
                    <text x="50" y="48" text-anchor="middle" class="font-sans" font-size="16" fill="var(--color-hud-gold)">
                        {{ Math.round(ram) }}%
                    </text>
                    <text x="50" y="62" text-anchor="middle" font-size="7" fill="var(--color-hud-dim)" letter-spacing="2">RAM</text>
                </svg>
            </div>
        </div>
    </div>
</template>
