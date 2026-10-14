<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps<{
    appName: string;
    modules?: Module[];
}>();

interface Module {
    id: number;
    title: string;
    description: string;
    stage: string;
    status: 'completed' | 'active' | 'locked' | 'available';
    score?: number;
    xp_reward?: number;
}

const moduleList = computed<Module[]>(() => {
    return props.modules || defaultModules;
});

const defaultModules: Module[] = [
    {
        id: 1,
        title: 'Dasar Klaim & Premis Argumen',
        description: 'Pelajari struktur argumen logis dan hindari logical fallacies dasar.',
        stage: 'Tahap 1',
        status: 'completed',
        score: 92,
    },
    {
        id: 2,
        title: 'Mekanisme & Rantai Kausalitas',
        description: 'Jelaskan bagaimana dan mengapa kebijakan atau proposal benar-benar berhasil.',
        stage: 'Tahap 2',
        status: 'active',
    },
    {
        id: 3,
        title: 'Rebuttal & Direct Clash',
        description: 'Menyerang premis utama lawan, bukan sekadar contoh sekunder.',
        stage: 'Tahap 3',
        status: 'locked',
    },
    {
        id: 4,
        title: 'Comparative Weighing & Metrics',
        description: 'Bandingkan dampak dua dunia secara proporsional dan persuasif.',
        stage: 'Tahap 4',
        status: 'locked',
    },
    {
        id: 5,
        title: 'Role Fulfillment BP & AP',
        description: 'Pahami peran spesifik PM, LO, DPM, DLO, Member, dan Whip.',
        stage: 'Tahap 5',
        status: 'locked',
    },
];
</script>

<template>
    <Head title="Belajar — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <header class="flex flex-col gap-1">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Jalur Pembelajaran</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">Modul &amp; Kurikulum Debat</h1>
                <p class="text-sm text-text-muted">Tuntaskan modul bertahap dari pemula hingga standar kompetisi WUDC.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-for="mod in moduleList"
                    :key="mod.id"
                    class="rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col justify-between gap-4 transition-all"
                    :class="{ 'border-primary-container ring-1 ring-primary-container/30': mod.status === 'active' }"
                >
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full"
                                :class="[
                                    mod.status === 'completed' ? 'bg-primary-light text-primary' :
                                    mod.status === 'active' ? 'bg-primary-container text-white' :
                                    'bg-surface-subtle text-text-muted'
                                ]"
                            >
                                {{ mod.stage }}
                            </span>
                            <span v-if="mod.score" class="text-xs font-bold text-primary">Skor: {{ mod.score }}/100</span>
                            <span v-else-if="mod.status === 'locked'" class="material-symbols-outlined text-text-muted text-lg" aria-hidden="true">lock</span>
                        </div>

                        <h2 class="text-lg font-bold text-on-surface">{{ mod.title }}</h2>
                        <p class="text-sm text-text-muted leading-relaxed">{{ mod.description }}</p>
                    </div>

                    <div class="pt-2 border-t border-border-clean/50 flex items-center justify-between">
                        <span class="text-xs text-text-muted">
                            {{ mod.status === 'completed' ? 'Selesai' : mod.status === 'active' ? 'Tersedia Sekarang' : 'Selesaikan modul sebelumnya' }}
                        </span>
                        <Link
                            v-if="mod.status !== 'locked'"
                            href="/practice"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container transition-all"
                            :class="mod.status === 'active' ? 'bg-primary-container text-white shadow-xs hover:bg-primary' : 'bg-surface-subtle text-on-surface hover:bg-surface-dim'"
                        >
                            <span>{{ mod.status === 'active' ? 'Mulai Modul' : 'Ulangi' }}</span>
                            <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
