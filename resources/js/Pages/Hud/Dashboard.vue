<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import axios from 'axios';
import ClockRing from '@/Components/Hud/ClockRing.vue';
import SystemMetrics from '@/Components/Hud/SystemMetrics.vue';
import JarvisLog from '@/Components/Hud/JarvisLog.vue';
import Crosshair from '@/Components/Hud/Crosshair.vue';
import HermesCapabilities from '@/Components/Hud/HermesCapabilities.vue';

const props = defineProps({
    user: { type: Object, default: () => ({}) },
    hermes_base_url: { type: String, default: '' },
});

const system = ref({ cpu: 0, ram: 0, cores: 0, load: [0, 0, 0] });
const hermesStatus = ref({ source: null, data: { status: 'unknown' } });
const hermesLogs = ref({ lines: [] });
const capabilitiesOpen = ref(false);

let sysTimer = null;
let hermesTimer = null;
let logTimer = null;

const fetchSystem = async () => {
    try {
        const { data } = await axios.get('/hud/api/system');
        system.value = data;
    } catch (e) { /* silent */ }
};
const fetchHermes = async () => {
    try {
        const { data } = await axios.get('/hud/api/hermes/status');
        hermesStatus.value = data;
    } catch (e) {
        hermesStatus.value = { source: null, data: { status: 'offline' } };
    }
};
const fetchLogs = async () => {
    try {
        const { data } = await axios.get('/hud/api/hermes/logs');
        hermesLogs.value = data;
    } catch (e) { /* silent */ }
};

const onKey = (e) => {
    if (e.key === 'Escape') capabilitiesOpen.value = false;
};

onMounted(() => {
    fetchSystem();
    fetchHermes();
    fetchLogs();
    sysTimer    = setInterval(fetchSystem, 2000);
    hermesTimer = setInterval(fetchHermes, 5000);
    logTimer    = setInterval(fetchLogs,   5000);
    window.addEventListener('keydown', onKey);
});
onBeforeUnmount(() => {
    clearInterval(sysTimer);
    clearInterval(hermesTimer);
    clearInterval(logTimer);
    window.removeEventListener('keydown', onKey);
});

const logout = () => router.post('/logout');
</script>

<template>
    <Head title="HUD" />

    <div class="relative h-screen w-screen overflow-hidden hud-scanlines">
        <div class="hud-scanline-bar"></div>

        <!-- Top bar -->
        <header class="absolute top-0 left-0 right-0 px-6 py-3 flex items-center justify-between z-20">
            <div class="flex items-center gap-3">
                <div class="font-sans text-lg tracking-[0.4em] hud-glow">J.E.F.E.R.S.O.N//HUD</div>
                <div class="font-mono text-[10px] opacity-60 uppercase">
                    pid {{ hermesStatus?.data?.pid || '—' }} ·
                    {{ hermesStatus?.data?.gateway_state || 'unknown' }}
                </div>
                <div class="flex items-center gap-1">
                    <span v-for="p in (hermesStatus?.data?.platforms || [])" :key="p.name"
                          class="font-mono text-[10px] uppercase px-1.5 py-0.5 rounded hud-border"
                          :style="{ color: p.state === 'connected' ? 'var(--color-hud-cyan)' : 'var(--color-hud-alert)' }">
                        {{ p.name }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="font-mono text-[10px] uppercase opacity-70">
                    operator: <span class="text-hud-gold">{{ user?.name || '—' }}</span>
                </div>
                <button @click="capabilitiesOpen = true"
                    class="hud-border rounded px-3 py-1 font-mono text-[10px] uppercase tracking-widest
                           hover:bg-cyan-500/10 transition-colors">
                    Manifest
                </button>
                <button @click="logout"
                    class="hud-border rounded px-3 py-1 font-mono text-[10px] uppercase tracking-widest
                           hover:bg-cyan-500/10 transition-colors">
                    Disengage
                </button>
            </div>
        </header>

        <!-- Center crosshair -->
        <div class="absolute inset-0 flex items-center justify-center z-0">
            <Crosshair />
        </div>

        <!-- Left column -->
        <aside class="absolute left-6 top-1/2 -translate-y-1/2 w-72 flex flex-col gap-4 z-10">
            <ClockRing />
            <SystemMetrics :cpu="system.cpu" :ram="system.ram" :cores="system.cores" />
        </aside>

        <!-- Right column -->
        <aside class="absolute right-6 top-20 bottom-6 w-1/4 z-10">
            <JarvisLog :lines="hermesLogs.lines || []" :hermes-status="hermesStatus" />
        </aside>

        <!-- Bottom strip -->
        <footer class="absolute bottom-0 left-0 right-0 px-6 py-2 z-20 flex justify-between font-mono text-[10px] uppercase opacity-60">
            <span>load: {{ (system.load || []).map(n => n.toFixed(2)).join(' / ') }}</span>
            <span>
                hermes: {{ hermesStatus?.data?.status || 'unknown' }}
                <span v-if="hermesStatus?.data?.age_seconds != null" class="opacity-60">
                    · last {{ hermesStatus.data.age_seconds }}s ago
                </span>
                <span v-if="hermesStatus?.data?.active_agents" class="opacity-80">
                    · agents {{ hermesStatus.data.active_agents }}
                </span>
            </span>
            <span :class="hermesStatus?.data?.status === 'online' ? 'hud-glow-gold' : ''"
                  :style="{ color: hermesStatus?.data?.status === 'online' ? 'var(--color-hud-gold)' : 'var(--color-hud-alert)' }">
                {{ hermesStatus?.data?.status === 'online' ? 'all systems nominal' : 'hermes link down' }}
            </span>
        </footer>

        <HermesCapabilities :open="capabilitiesOpen" :hermes-status="hermesStatus"
                            @close="capabilitiesOpen = false" />
    </div>
</template>