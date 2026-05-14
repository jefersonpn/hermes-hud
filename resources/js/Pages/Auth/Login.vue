<script setup>
import { useForm, Head } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Access" />

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="hud-scanline-bar"></div>

        <div class="absolute inset-0 flex items-center justify-center opacity-30 pointer-events-none">
            <svg viewBox="0 0 600 600" class="w-[80vmin] h-[80vmin]">
                <g class="anim-spin-slow" style="transform-origin: 300px 300px">
                    <circle cx="300" cy="300" r="280" fill="none" stroke="var(--color-hud-cyan)" stroke-width="1"
                            stroke-dasharray="4 8 24 4 60 6" />
                </g>
                <g class="anim-spin-fast" style="transform-origin: 300px 300px">
                    <circle cx="300" cy="300" r="220" fill="none" stroke="var(--color-hud-gold)" stroke-width="1"
                            stroke-dasharray="2 8" opacity="0.6" />
                </g>
            </svg>
        </div>

        <form @submit.prevent="submit"
              class="relative z-10 w-full max-w-sm hud-panel hud-border rounded-md p-8 hud-scanlines">
            <div class="text-center mb-6">
                <div class="font-sans text-2xl tracking-[0.4em] hud-glow anim-flicker">HERMES // HUD</div>
                <div class="font-mono text-[10px] opacity-60 mt-2 uppercase">Biometric handshake required</div>
            </div>

            <label class="block mb-4">
                <span class="font-mono text-[10px] uppercase opacity-70 tracking-widest">Identifier</span>
                <input v-model="form.email" type="email" autocomplete="email" required
                    class="mt-1 w-full bg-transparent hud-border rounded px-3 py-2 font-mono text-sm
                           text-hud-cyan focus:outline-none focus:ring-1 focus:ring-cyan-400" />
                <div v-if="form.errors.email" class="text-xs mt-1" style="color: var(--color-hud-alert)">{{ form.errors.email }}</div>
            </label>

            <label class="block mb-6">
                <span class="font-mono text-[10px] uppercase opacity-70 tracking-widest">Passphrase</span>
                <input v-model="form.password" type="password" autocomplete="current-password" required
                    class="mt-1 w-full bg-transparent hud-border rounded px-3 py-2 font-mono text-sm
                           text-hud-cyan focus:outline-none focus:ring-1 focus:ring-cyan-400" />
                <div v-if="form.errors.password" class="text-xs mt-1" style="color: var(--color-hud-alert)">{{ form.errors.password }}</div>
            </label>

            <label class="flex items-center gap-2 mb-6 font-mono text-[10px] uppercase opacity-70">
                <input v-model="form.remember" type="checkbox" class="accent-cyan-400" />
                <span>Remember this terminal</span>
            </label>

            <button type="submit" :disabled="form.processing"
                class="w-full hud-border rounded py-2 font-sans tracking-[0.3em] uppercase
                       hover:bg-cyan-500/10 transition-colors hud-glow anim-pulse disabled:opacity-40">
                {{ form.processing ? 'Authenticating…' : 'Engage' }}
            </button>

            <div class="mt-6 text-center font-mono text-[10px] opacity-50">
                © Stark Industries — restricted access
            </div>
        </form>
    </div>
</template>
