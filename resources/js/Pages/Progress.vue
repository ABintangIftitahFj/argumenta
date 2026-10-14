<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps<{
    appName: string;
}>();

interface SpeechRecord {
    id: number;
    motion: string;
    format: 'BP' | 'AP';
    role: string;
    date: string;
    score: number;
    duration: string;
}

const speeches: SpeechRecord[] = [
    {
        id: 1,
        motion: 'Dewan ini akan mewajibkan transparansi penuh model AI publik',
        format: 'BP',
        role: 'Prime Minister (PM)',
        date: 'Hari ini',
        score: 84,
        duration: '6m 45s',
    },
    {
        id: 2,
        motion: 'Dewan ini menyesalkan komersialisasi riset akademik',
        format: 'BP',
        role: 'Leader of Opposition (LO)',
        date: 'Kemarin',
        score: 79,
        duration: '7m 10s',
    },
    {
        id: 3,
        motion: 'Sebagai negara berkembang, dewan ini akan memprioritaskan industrialisasi hijau',
        format: 'AP',
        role: 'Prime Minister (PM)',
        date: '3 hari lalu',
        score: 82,
        duration: '7m 00s',
    },
];

const rubricScores = [
    { category: 'Matter (Substansi & Bukti)', score: 82, max: 100, color: 'bg-primary-container' },
    { category: 'Manner (Retorika & Kepercayaan Diri)', score: 78, max: 100, color: 'bg-secondary' },
    { category: 'Method (Struktur AREL & Respons Tim)', score: 85, max: 100, color: 'bg-primary' },
    { category: 'Rebuttal & Clash Effectiveness', score: 80, max: 100, color: 'bg-accent-orange' },
];

const badges = [
    { name: '5-Day Streak', icon: '🔥', description: 'Latihan 5 hari beruntun tanpa jeda' },
    { name: 'AREL Master', icon: '🎯', description: 'Menyelesaikan 10 pidato dengan struktur lengkap' },
    { name: 'BP Debutant', icon: '🏛️', description: 'Menuntaskan simulasi pertama 4 tim BP' },
    { name: 'Crisp Speaker', icon: '🎙️', description: 'Penyampaian ritme konstan 130-150 kata/menit' },
];
</script>

<template>
    <Head title="Progres &amp; Statistik — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary">Rekam Jejak &amp; Analitik</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">Progres Latihan Anda</h1>
                    <p class="text-sm text-text-muted">Pantau evaluasi pidato, streak konsistensi, dan penguasaan rubrik debat Anda.</p>
                </div>

                <Link
                    href="/practice"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-xs hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container self-start sm:self-auto"
                >
                    <span class="material-symbols-outlined text-base" aria-hidden="true">add</span>
                    <span>Latihan Baru</span>
                </Link>
            </header>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-1">
                    <span class="text-xs font-semibold text-text-muted">Total Pidato</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">14</span>
                    <span class="text-[11px] text-primary font-bold mt-1">↑ +3 minggu ini</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-1">
                    <span class="text-xs font-semibold text-text-muted">Current Streak</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">5</span>
                        <span class="text-lg">🔥</span>
                    </div>
                    <span class="text-[11px] text-accent-orange font-bold mt-1">Rekor pribadi: 8 hari</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-1">
                    <span class="text-xs font-semibold text-text-muted">Rata-rata Skor</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-primary font-serif">81.6</span>
                    <span class="text-[11px] text-text-muted font-medium mt-1">Skala standar WUDC 70-85</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-1">
                    <span class="text-xs font-semibold text-text-muted">Total XP</span>
                    <span class="text-2xl sm:text-3xl font-extrabold text-secondary font-serif">560</span>
                    <span class="text-[11px] text-text-muted font-medium mt-1">Target Level 2: 750 XP</span>
                </div>
            </div>

            <!-- Rubric Competency & Badges Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Rubric Breakdown -->
                <div class="lg:col-span-2 p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-on-surface font-serif">Penguasaan Kategori Rubrik</h2>
                        <span class="text-xs text-text-muted">Berdasarkan 5 pidato terakhir</span>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div v-for="item in rubricScores" :key="item.category" class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-on-surface">{{ item.category }}</span>
                                <span class="text-primary">{{ item.score }} / {{ item.max }}</span>
                            </div>
                            <div class="w-full h-2.5 rounded-full bg-surface-subtle overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :class="item.color"
                                    :style="{ width: `${item.score}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges Earned -->
                <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-on-surface font-serif">Lencana Pencapaian</h2>
                        <span class="text-xs font-bold text-primary">4 Diraih</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div
                            v-for="badge in badges"
                            :key="badge.name"
                            class="p-3 rounded-2xl bg-surface-subtle border border-border-clean flex flex-col items-center text-center gap-1"
                        >
                            <span class="text-2xl" aria-hidden="true">{{ badge.icon }}</span>
                            <span class="text-xs font-bold text-on-surface mt-1 leading-tight">{{ badge.name }}</span>
                            <span class="text-[10px] text-text-muted leading-tight line-clamp-2">{{ badge.description }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Speeches Table / List -->
            <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-on-surface font-serif">Riwayat Latihan Pidato</h2>
                    <span class="text-xs font-semibold text-text-muted">3 Sesi Terakhir</span>
                </div>

                <div class="flex flex-col divide-y divide-border-clean/50">
                    <div
                        v-for="item in speeches"
                        :key="item.id"
                        class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                    >
                        <div class="flex flex-col gap-1 max-w-xl">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-secondary/15 text-secondary text-[10px] font-extrabold uppercase tracking-wide">
                                    {{ item.format }}
                                </span>
                                <span class="text-xs font-semibold text-text-muted">• {{ item.role }}</span>
                                <span class="text-xs font-semibold text-text-muted">• {{ item.date }}</span>
                            </div>
                            <span class="text-sm font-bold text-on-surface leading-snug">"{{ item.motion }}"</span>
                            <span class="text-xs text-text-muted">Durasi pidato: {{ item.duration }}</span>
                        </div>

                        <div class="flex items-center gap-3 self-end sm:self-center">
                            <div class="flex flex-col items-end">
                                <span class="text-lg font-extrabold text-primary font-serif">{{ item.score }}/100</span>
                                <span class="text-[10px] text-text-muted">Skor Keseluruhan</span>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-surface-subtle border border-border-clean text-xs font-bold hover:bg-surface-dim transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            >
                                Detail Feedback
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
