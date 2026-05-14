<script setup>
import { ref, watch, nextTick, onMounted } from 'vue';

const props = defineProps({
    lines: { type: Array, default: () => [] },
    hermesStatus: { type: Object, default: () => ({}) },
});

const local = ref([]);
const scroller = ref(null);

const push = (txt) => {
    const t = new Date().toLocaleTimeString('it-IT', { hour12: false });
    local.value.push({ t, txt });
    if (local.value.length > 120) local.value.shift();
};

onMounted(() => {
    push('Good evening, Sir.');
    push('Initializing HUD overlay…');
    push('Linking to Hermes Agent…');
});

watch(
    () => props.hermesStatus,
    (s) => {
        if (!s) return;
        if (s.data?.status === 'offline') {
            push('[offline] Hermes Agent non risponde.');
        } else if (s.source) {
            push(`Hermes Agent online via ${s.source}.`);
        }
    },
    { deep: true }
);

const seenSig = new Set();
watch(
    () => props.lines,
    (arr) => {
        if (!Array.isArray(arr)) return;
        for (const l of arr.slice(-12)) {
            const sig = (l.ts || '') + '|' + (l.message || JSON.stringify(l));
            if (seenSig.has(sig)) continue;
            seenSig.add(sig);
            const txt = typeof l === 'string'
                ? l
                : (l.level ? `[${l.level}] ${l.message}` : (l.message || JSON.stringify(l)));
            push(txt);
        }
        if (seenSig.size > 400) {
            const arr2 = Array.from(seenSig).slice(-200);
            seenSig.clear();
            arr2.forEach(s => seenSig.add(s));
        }
    },
    { deep: true }
);

watch(
    () => local.value.length,
    async () => {
        await nextTick();
        if (scroller.value) scroller.value.scrollTop = scroller.value.scrollHeight;
    }
);
</script>

<template>
    <div class="hud-panel hud-border rounded-md p-3 h-full flex flex-col">
        <div class="flex items-center justify-between mb-2">
            <div class="font-mono text-xs uppercase opacity-80 tracking-widest">[ H.E.R.M.E.S // LOG ]</div>
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full anim-pulse"
                    :style="{ background: hermesStatus?.data?.status === 'offline' ? 'var(--color-hud-alert)' : 'var(--color-hud-cyan)' }"></span>
                <span class="font-mono text-[10px] opacity-60">
                    {{ hermesStatus?.data?.status === 'offline' ? 'OFFLINE' : 'ONLINE' }}
                </span>
            </div>
        </div>
        <div ref="scroller" class="flex-1 overflow-y-auto font-mono text-[11px] leading-relaxed pr-2 hud-scanlines">
            <div v-for="(l, i) in local" :key="i" class="flex gap-2"
                 :style="{ opacity: Math.max(0.4, 1 - (local.length - 1 - i) * 0.04) }">
                <span class="opacity-50">{{ l.t }}</span>
                <span class="text-hud-cyan">›</span>
                <span class="break-all">{{ l.txt }}</span>
            </div>
        </div>
    </div>
</template>
