<script setup></script>

<template>
    <div class="relative w-[420px] h-[420px] pointer-events-none">
        <svg viewBox="0 0 400 400" class="absolute inset-0 w-full h-full">
            <g class="anim-spin-slow" style="transform-origin: 200px 200px">
                <circle cx="200" cy="200" r="190" fill="none" stroke="var(--color-hud-cyan)" stroke-width="1" opacity="0.45"
                    stroke-dasharray="3 4 8 4 28 6 4 4 60 4" />
                <g fill="var(--color-hud-cyan)" opacity="0.6">
                    <rect x="199" y="6"   width="2" height="14" />
                    <rect x="199" y="380" width="2" height="14" />
                    <rect x="6"   y="199" width="14" height="2" />
                    <rect x="380" y="199" width="14" height="2" />
                </g>
            </g>
            <g class="anim-spin-med" style="transform-origin: 200px 200px">
                <circle cx="200" cy="200" r="150" fill="none" stroke="var(--color-hud-cyan-soft)" stroke-width="1" opacity="0.55"
                    stroke-dasharray="6 8" />
                <g transform="translate(200 200)">
                    <polygon points="0,-150 -6,-138 6,-138" fill="var(--color-hud-cyan)" opacity="0.9" />
                </g>
            </g>
            <g class="anim-spin-fast" style="transform-origin: 200px 200px">
                <circle cx="200" cy="200" r="110" fill="none" stroke="var(--color-hud-gold)" stroke-width="1" opacity="0.5"
                    stroke-dasharray="2 6" />
            </g>
            <g class="anim-pulse">
                <circle cx="200" cy="200" r="70" fill="none" stroke="var(--color-hud-cyan)" stroke-width="1" opacity="0.7" />
                <circle cx="200" cy="200" r="50" fill="none" stroke="var(--color-hud-cyan)" stroke-width="1" opacity="0.6" />
                <line x1="200" y1="150" x2="200" y2="180" stroke="var(--color-hud-cyan)" stroke-width="1" />
                <line x1="200" y1="220" x2="200" y2="250" stroke="var(--color-hud-cyan)" stroke-width="1" />
                <line x1="150" y1="200" x2="180" y2="200" stroke="var(--color-hud-cyan)" stroke-width="1" />
                <line x1="220" y1="200" x2="250" y2="200" stroke="var(--color-hud-cyan)" stroke-width="1" />
                <circle cx="200" cy="200" r="3" fill="var(--color-hud-cyan)" />
            </g>
        </svg>
    </div>
</template>
