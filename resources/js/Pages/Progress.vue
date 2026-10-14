<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

interface Stats {
    total_speeches: number;
    total_practice_seconds: number;
    formatted_practice_time: string;
    average_score: number;
    matter_average: number;
    manner_average: number;
    method_average: number;
    streak: number;
    xp_points: number;
}

interface HistoryItem {
    session_id: number;
    motion: string;
    format: string;
    role: string;
    language: string;
    overall_score: number | null;
    duration_seconds: number;
    formatted_duration: string;
    date: string;
    evaluation_url: string;
}

interface TrendItem {
    evaluation_id: number;
    debate_format: string;
    role: string;
    overall_score: number;
    matter_score: number;
    manner_score: number;
    method_score: number;
    language_score: number | null;
    date: string;
}

interface ErrorSample {
    title: string;
    quote: string | null;
    fix: string | null;
    severity: string;
}

interface ErrorItem {
    category: string;
    count: number;
    label: string;
    samples: ErrorSample[];
}

const props = defineProps<{
    appName: string;
    stats: Stats;
    history: HistoryItem[];
    skillTrends: TrendItem[];
    errorFrequency: ErrorItem[];
    nextRecommendation: string;
}>();
</script>

<template>
    <Head title="Kemajuan & Analisis Debat — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-8 max-w-5xl mx-auto pb-12">
            <!-- Header Section -->
            <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-on-surface">Analisis Kemajuan & Statistik</h1>
                    <p class="text-xs text-text-muted mt-1">
                        Pantau riwayat pidato, grafik evolusi skor, dan evaluasi konsistensi argumen Anda.
                    </p>
                </div>

                <Link
                    href="/practice"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-primary-container text-white font-bold text-xs shadow-sm hover:bg-primary transition-all min-h-[44px] shrink-0"
                >
                    <span class="material-symbols-outlined text-lg">mic</span>
                    <span>Mulai Sesi Baru</span>
                </Link>
            </header>

            <!-- 4 Core Metric KPI Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center justify-between text-text-muted">
                        <span class="text-xs font-bold">Total Pidato</span>
                        <span class="material-symbols-outlined text-primary text-xl">record_voice_over</span>
                    </div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-on-surface">{{ stats.total_speeches }}</span>
                        <span class="text-xs font-bold text-text-muted">sesi</span>
                    </div>
                    <span class="text-[11px] text-text-muted">{{ stats.formatted_practice_time }} total waktu</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center justify-between text-text-muted">
                        <span class="text-xs font-bold">Rerata Skor Juri</span>
                        <span class="material-symbols-outlined text-accent-orange text-xl">grade</span>
                    </div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-on-surface">{{ stats.average_score }}</span>
                        <span class="text-xs font-bold text-text-muted">/ 100</span>
                    </div>
                    <span class="text-[11px] text-primary font-bold">Standar WUDC Speaker Scale</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center justify-between text-text-muted">
                        <span class="text-xs font-bold">Streak Latihan</span>
                        <span class="material-symbols-outlined text-amber-500 text-xl">local_fire_department</span>
                    </div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-on-surface">{{ stats.streak }}</span>
                        <span class="text-xs font-bold text-text-muted">hari</span>
                    </div>
                    <span class="text-[11px] text-text-muted">Pertahankan konsistensi harian</span>
                </div>

                <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-2">
                    <div class="flex items-center justify-between text-text-muted">
                        <span class="text-xs font-bold">Pengalaman (XP)</span>
                        <span class="material-symbols-outlined text-teal-600 text-xl">bolt</span>
                    </div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-on-surface">{{ stats.xp_points }}</span>
                        <span class="text-xs font-bold text-text-muted">XP</span>
                    </div>
                    <span class="text-[11px] text-text-muted">Naik level bersama Kiko</span>
                </div>
            </div>

            <!-- Next Recommendation CTA Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-primary-light/60 to-primary-light/20 border-2 border-primary/30 flex flex-col sm:flex-row items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-primary-container text-white flex items-center justify-center shrink-0 shadow-sm">
                        <span class="material-symbols-outlined text-3xl">psychology</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-black uppercase text-primary tracking-wider">Rekomendasi Latihan Terpersonalisasi</span>
                        <h2 class="text-sm font-bold text-on-surface mt-0.5 max-w-xl leading-relaxed">
                            {{ nextRecommendation }}
                        </h2>
                    </div>
                </div>

                <Link
                    href="/learn"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-xs shadow-sm hover:bg-primary transition-all min-h-[44px] shrink-0"
                >
                    <span>Pelajari Modul</span>
                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                </Link>
            </div>

            <!-- Two-Column Section: Skill Trends & Rubric Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Skill Evolution & Dimension Rubrics (7 cols) -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    <!-- Rubric Breakdown -->
                    <div class="p-6 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">tune</span>
                                <span>Distribusi Rerata Dimensi Debat</span>
                            </h3>
                            <span class="text-[11px] text-text-muted">Skala WUDC & AP</span>
                        </div>

                        <div class="flex flex-col gap-4 mt-2">
                            <!-- Matter -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-on-surface">Matter (Substansi, Bukti & Logika AREL)</span>
                                    <span class="text-primary font-black">{{ stats.matter_average }}</span>
                                </div>
                                <div class="w-full h-3 rounded-full bg-surface-subtle overflow-hidden">
                                    <div
                                        class="h-full bg-primary-container rounded-full transition-all duration-500"
                                        :style="{ width: `${Math.min(100, (stats.matter_average / 40) * 100)}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Manner -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-on-surface">Manner (Retorika, Intonasi & Ritme Suara)</span>
                                    <span class="text-accent-orange font-black">{{ stats.manner_average }}</span>
                                </div>
                                <div class="w-full h-3 rounded-full bg-surface-subtle overflow-hidden">
                                    <div
                                        class="h-full bg-accent-orange rounded-full transition-all duration-500"
                                        :style="{ width: `${Math.min(100, (stats.manner_average / 40) * 100)}%` }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Method -->
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span class="text-on-surface">Method (Struktur Pidato & Signposting)</span>
                                    <span class="text-teal-600 font-black">{{ stats.method_average }}</span>
                                </div>
                                <div class="w-full h-3 rounded-full bg-surface-subtle overflow-hidden">
                                    <div
                                        class="h-full bg-teal-600 rounded-full transition-all duration-500"
                                        :style="{ width: `${Math.min(100, (stats.method_average / 20) * 100)}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trend Timeline (Saved Data Points) -->
                    <div class="p-6 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">trending_up</span>
                                <span>Tren Skor Sepanjang Waktu</span>
                            </h3>
                            <span class="text-[11px] text-text-muted">{{ skillTrends.length }} data sesi</span>
                        </div>

                        <!-- Empty state if no trends -->
                        <div v-if="skillTrends.length === 0" class="p-8 text-center flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-text-muted/50">monitoring</span>
                            <p class="text-xs text-text-muted font-medium">Belum ada data skor yang terekam.</p>
                            <Link href="/practice" class="text-xs font-bold text-primary hover:underline mt-1">Mulai latihan sekarang &rarr;</Link>
                        </div>

                        <!-- Visual Trend Cards -->
                        <div v-else class="flex flex-col gap-3">
                            <div
                                v-for="(trend, i) in skillTrends"
                                :key="trend.evaluation_id"
                                class="p-3.5 rounded-2xl bg-surface-subtle border border-border-clean/60 flex items-center justify-between text-xs"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary-light text-primary font-black text-[11px] flex items-center justify-center">
                                        {{ i + 1 }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-on-surface">{{ trend.role }} ({{ trend.debate_format }})</div>
                                        <div class="text-[10px] text-text-muted">{{ trend.date }}</div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="hidden sm:flex items-center gap-2 text-[11px] text-text-muted">
                                        <span>M: {{ trend.matter_score }}</span>
                                        <span>•</span>
                                        <span>G: {{ trend.manner_score }}</span>
                                        <span>•</span>
                                        <span>S: {{ trend.method_score }}</span>
                                    </div>
                                    <div class="text-sm font-black text-primary px-3 py-1 rounded-xl bg-primary-light">
                                        {{ trend.overall_score }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Frequency & History (5 cols) -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <!-- Most Common Error Frequency -->
                    <div class="p-6 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-accent-orange text-xl">error</span>
                                <span>Pola Kelemahan yang Sering Muncul</span>
                            </h3>
                            <span class="text-[11px] text-text-muted">Analisis AI Judge</span>
                        </div>

                        <div v-if="errorFrequency.length === 0" class="p-6 text-center text-xs text-text-muted">
                            Belum ada catatan kelemahan yang terdeteksi. Selesaikan pidato untuk melihat diagnosis.
                        </div>

                        <div v-else class="flex flex-col gap-3">
                            <div
                                v-for="err in errorFrequency"
                                :key="err.category"
                                class="p-4 rounded-2xl bg-surface-subtle border border-border-clean/60 flex flex-col gap-2"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-on-surface">{{ err.label }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-accent-orange/15 text-accent-orange">
                                        {{ err.count }}x terdeteksi
                                    </span>
                                </div>

                                <div v-if="err.samples.length > 0" class="text-[11px] text-text-muted flex flex-col gap-1 mt-1">
                                    <div v-for="(sample, sIdx) in err.samples" :key="sIdx" class="italic">
                                        "{{ sample.title }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Speech History -->
                    <div class="p-6 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black text-on-surface flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">history</span>
                                <span>Riwayat Sesi Pidato</span>
                            </h3>
                            <span class="text-[11px] text-text-muted">{{ history.length }} terbaru</span>
                        </div>

                        <div v-if="history.length === 0" class="p-6 text-center text-xs text-text-muted">
                            Belum ada riwayat pidato yang diselesaikan.
                        </div>

                        <div v-else class="flex flex-col gap-3">
                            <Link
                                v-for="item in history"
                                :key="item.session_id"
                                :href="item.evaluation_url"
                                class="p-4 rounded-2xl bg-surface-subtle hover:bg-surface-dim border border-border-clean/60 transition-all flex flex-col gap-1.5 focus-visible:outline-2 focus-visible:outline-primary-container"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-text-muted">
                                        <span class="px-2 py-0.5 rounded-full bg-primary-light text-primary font-black uppercase">{{ item.format }}</span>
                                        <span>{{ item.role }}</span>
                                    </div>
                                    <div v-if="item.overall_score" class="text-xs font-black text-primary">
                                        Skor: {{ item.overall_score }}
                                    </div>
                                </div>

                                <div class="text-xs font-bold text-on-surface line-clamp-2">
                                    "{{ item.motion }}"
                                </div>

                                <div class="flex items-center justify-between text-[10px] text-text-muted mt-1">
                                    <span>{{ item.date }}</span>
                                    <span>{{ item.formatted_duration }}</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
