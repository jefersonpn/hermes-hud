<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    hermesStatus: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['close']);

const activeCat = ref(null);

const platformState = (name) => {
    const p = (props.hermesStatus?.data?.platforms || []).find(
        (x) => (x.name || '').toLowerCase() === name.toLowerCase()
    );
    return p?.state || null;
};

const categories = computed(() => [
    {
        id: 'gateways',
        title: 'Gateway · Comunicazione',
        color: 'cyan',
        items: [
            {
                name: 'WhatsApp',
                desc: 'Bridge Baileys (self-chat). Pairing: hermes whatsapp. Sessione in ~/.hermes/whatsapp/session.',
                live: platformState('whatsapp'),
            },
            {
                name: 'Telegram',
                desc: 'Bot gateway built-in. Token in .env, attivabile via config gateway.',
                live: platformState('telegram'),
            },
            {
                name: 'Discord',
                desc: 'Bot gateway built-in. Richiede token Discord nel .env.',
                live: platformState('discord'),
            },
            {
                name: 'API Server HTTP',
                desc: 'Endpoint REST/WS interni del gateway per stato e messaggistica.',
                live: platformState('api_server'),
            },
        ],
    },
    {
        id: 'ide',
        title: 'IDE · Coding',
        color: 'gold',
        items: [
            {
                name: 'ACP server (stdio)',
                desc: 'hermes acp · agente esterno per Cursor/Zed via stdio. Wrapper SSH: hermes-acp-login.',
            },
            {
                name: 'MCP server (stdio)',
                desc: 'hermes mcp serve · espone tool Hermes come server MCP per qualsiasi client compatibile.',
            },
            {
                name: 'SSH bridge LAN',
                desc: 'Cursor remoti (Mac/Win) si collegano via ssh jeferson@192.168.2.73 + wrapper acp-login.',
            },
        ],
    },
    {
        id: 'models',
        title: 'Modelli · LLM',
        color: 'cyan',
        items: [
            {
                name: 'LM Studio locale',
                desc: 'Provider default. Modello attivo: qwen2.5-coder-32b-instruct, ctx 65536 (≥64k obbligatorio).',
            },
            {
                name: 'Cloud provider',
                desc: 'Switch via config.yaml: Gemini, OpenAI, Anthropic, Ollama. Chiavi in ~/.hermes/.env.',
            },
            {
                name: 'lms CLI loader',
                desc: 'Carica modelli con context length stabile (evita bug GUI sticky 4096).',
            },
        ],
    },
    {
        id: 'memory',
        title: 'Memoria · RAG',
        color: 'gold',
        items: [
            {
                name: 'Sessioni JSON',
                desc: '~/.hermes/sessions/*.json · storia conversazioni per ogni canale/utente.',
            },
            {
                name: 'session_rag.py hook',
                desc: 'pre_llm_call: estrae keyword e fa grep sulle sessioni, inietta snippet nel prompt.',
            },
            {
                name: 'Kanban DB',
                desc: '~/.hermes/kanban.db · tracking task interno per agenti.',
            },
            {
                name: 'state.db',
                desc: 'SQLite di stato persistente runtime.',
            },
        ],
    },
    {
        id: 'skills',
        title: 'Skills · Hooks',
        color: 'cyan',
        items: [
            {
                name: 'Skills custom',
                desc: '~/.hermes/skills/ · script invocabili dall\'agente come tool extra.',
            },
            {
                name: 'pre_llm_call hooks',
                desc: 'Iniettano contesto prima della chiamata LLM. Risposte concatenate con doppio newline.',
            },
            {
                name: 'post_llm_call hooks',
                desc: 'Post-processano l\'output del modello (filtri, trasformazioni, log strutturati).',
            },
            {
                name: 'hooks_auto_accept',
                desc: 'Bypassa prompt di approvazione (necessario su WhatsApp/headless).',
            },
        ],
    },
    {
        id: 'exec',
        title: 'Esecuzione · Tool',
        color: 'gold',
        items: [
            {
                name: 'Shell exec',
                desc: 'Esegue comandi sul Mac (rispetta autorizzazioni della config agent).',
            },
            {
                name: 'Filesystem R/W',
                desc: 'Lettura/scrittura file locali per editing e analisi.',
            },
            {
                name: 'Web fetch',
                desc: 'Scarica pagine/JSON da URL per ricerca e contesto runtime.',
            },
            {
                name: 'session_search',
                desc: 'Tool nativo di recall sessioni (fragile su Qwen → bypassato dal RAG hook).',
            },
        ],
    },
    {
        id: 'cli',
        title: 'CLI · Dashboard',
        color: 'cyan',
        items: [
            {
                name: 'hermes dashboard',
                desc: 'FastAPI web 127.0.0.1:9119 · flag: --port --host --no-open --insecure --status --stop.',
            },
            {
                name: 'hermes gateway',
                desc: 'Avvia/gestisce i gateway. Su questo Mac gira come servizio launchd.',
            },
            {
                name: 'hermes acp / mcp',
                desc: 'Server stdio per IDE e client MCP.',
            },
            {
                name: 'hermes whatsapp',
                desc: 'Wizard di pairing del bridge Baileys.',
            },
        ],
    },
]);

const colorFor = (c) =>
    c === 'gold' ? 'var(--color-hud-gold)' : 'var(--color-hud-cyan)';
</script>

<template>
    <Transition name="hud-fade">
        <div v-if="open"
             class="absolute inset-0 z-30 flex items-center justify-center"
             style="background: rgba(0, 8, 20, 0.78); backdrop-filter: blur(3px);">
            <div class="hud-panel hud-border rounded-md p-5 w-[min(1100px,92vw)] h-[min(78vh,720px)]
                        flex flex-col relative hud-scanlines">
                <!-- header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="font-sans text-base tracking-[0.4em] hud-glow">
                            HERMES // CAPABILITY MANIFEST
                        </div>
                        <div class="font-mono text-[10px] uppercase opacity-60">
                            v0.13.0 · LAN 192.168.2.73
                        </div>
                    </div>
                    <button @click="emit('close')"
                            class="hud-border rounded px-3 py-1 font-mono text-[10px] uppercase tracking-widest
                                   hover:bg-cyan-500/10 transition-colors">
                        Close [esc]
                    </button>
                </div>

                <!-- grid of categories -->
                <div class="flex-1 overflow-y-auto pr-2 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 auto-rows-min">
                    <section v-for="cat in categories" :key="cat.id"
                             class="hud-border rounded p-3"
                             :style="{ borderColor: colorFor(cat.color) + '55' }">
                        <header class="flex items-center justify-between mb-2">
                            <div class="font-mono text-[11px] uppercase tracking-widest"
                                 :style="{ color: colorFor(cat.color) }">
                                [ {{ cat.title }} ]
                            </div>
                            <div class="font-mono text-[10px] opacity-50">
                                {{ cat.items.length }}
                            </div>
                        </header>
                        <ul class="flex flex-col gap-2">
                            <li v-for="item in cat.items" :key="item.name"
                                class="font-mono text-[11px] leading-snug">
                                <div class="flex items-center gap-2">
                                    <span :style="{ color: colorFor(cat.color) }">›</span>
                                    <span class="font-sans tracking-wider text-[11px]"
                                          :style="{ color: colorFor(cat.color) }">
                                        {{ item.name }}
                                    </span>
                                    <span v-if="item.live"
                                          class="ml-auto font-mono text-[9px] uppercase px-1.5 py-0.5 rounded"
                                          :style="{
                                            color: item.live === 'connected' ? 'var(--color-hud-cyan)' : 'var(--color-hud-alert)',
                                            border: '1px solid currentColor',
                                          }">
                                        {{ item.live }}
                                    </span>
                                </div>
                                <div class="opacity-70 pl-4 mt-0.5 break-words">
                                    {{ item.desc }}
                                </div>
                            </li>
                        </ul>
                    </section>
                </div>

                <!-- footer hint -->
                <div class="mt-3 font-mono text-[10px] uppercase opacity-50 text-center">
                    Stato gateway live letto da ~/.hermes/gateway_state.json
                    · capability = ciò che il binario hermes espone, non garanzia di abilitazione runtime.
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.hud-fade-enter-active,
.hud-fade-leave-active {
    transition: opacity 0.18s ease;
}
.hud-fade-enter-from,
.hud-fade-leave-to {
    opacity: 0;
}
</style>
