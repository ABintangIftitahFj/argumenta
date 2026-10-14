<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps<{
    appName: string;
}>();
</script>

<template>
    <Head title="Beranda — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <!-- Hero Mascot Card -->
            <section
                class="relative overflow-hidden rounded-3xl bg-surface-white p-6 sm:p-8 shadow-sm border border-border-clean/60 flex flex-col md:flex-row items-center justify-between gap-6"
                aria-labelledby="hero-heading"
            >
                <div class="flex flex-col items-start gap-3 max-w-xl z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse" aria-hidden="true"></span>
                        <span>Jalur Latihan BP &amp; AP</span>
                    </div>

                    <h1 id="hero-heading" class="text-2xl sm:text-4xl font-extrabold text-on-surface tracking-tight leading-tight font-serif">
                        Latih argumen. Bicara lebih kuat bersama Kiko.
                    </h1>

                    <p class="text-sm sm:text-base text-text-muted leading-relaxed">
                        {{ appName }} siap mendampingi latihan debat mandiri dengan analisis mosi, struktur argumen, dan feedback juri berbasis standar WUDC.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <Link
                            href="/practice"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                        >
                            <span class="material-symbols-outlined text-xl" aria-hidden="true">play_circle</span>
                            <span>Mulai Latihan Mandiri</span>
                        </Link>

                        <Link
                            href="/coach"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-surface-subtle text-on-surface font-bold text-sm hover:bg-surface-dim transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                        >
                            <span class="material-symbols-outlined text-xl text-primary" aria-hidden="true">smart_toy</span>
                            <span>Tanya Coach Kiko</span>
                        </Link>
                    </div>
                </div>

                <!-- Mascot Visual Badge -->
                <div class="relative shrink-0 flex items-center justify-center w-36 h-36 sm:w-44 sm:h-44">
                    <div class="absolute inset-0 rounded-full bg-primary-light blur-xl -z-0" aria-hidden="true"></div>
                    <img
                        src="/images/maskot.png"
                        alt="Maskot Kiko"
                        class="w-32 h-32 sm:w-40 sm:h-40 object-contain drop-shadow-md z-10 transition-transform duration-300 hover:scale-105"
                        onerror="this.style.display='none'"
                    />
                </div>
            </section>

            <!-- 2-Column Responsive Grid: Daily Target & Learning Modules -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Daily Goal Card -->
                <div class="rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col justify-between gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl" aria-hidden="true">target</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-on-surface">Target Harian</h2>
                                <p class="text-xs text-text-muted">1 dari 2 latihan selesai</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-accent-orange/15 text-on-surface text-xs font-bold">
                            ⚡ +25 XP
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="flex flex-col gap-1.5">
                        <div class="w-full bg-surface-subtle h-3 rounded-full overflow-hidden p-0.5">
                            <div class="bg-primary-container h-full rounded-full transition-all duration-500 ease-out" style="width: 50%;"></div>
                        </div>
                        <div class="flex justify-between text-xs text-text-muted font-medium">
                            <span>50% Selesai</span>
                            <span>1 Latihan Lagi</span>
                        </div>
                    </div>

                    <!-- Streak Row -->
                    <div class="pt-2 border-t border-border-clean/50 flex items-center justify-between text-xs font-medium text-text-muted">
                        <span class="flex items-center gap-1 text-on-surface font-bold">
                            🔥 5 Hari Berturut-turut
                        </span>
                        <span class="text-primary font-semibold">Pertahankan!</span>
                    </div>
                </div>

                <!-- Learning Path Preview -->
                <div class="lg:col-span-2 rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col justify-between gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-secondary-container text-secondary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl" aria-hidden="true">route</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-on-surface">Jalur Belajar: Fundamentals</h2>
                                <p class="text-xs text-text-muted">Modul 1: Dasar Klaim &amp; Mekanisme</p>
                            </div>
                        </div>

                        <Link
                            href="/learn"
                            class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        >
                            <span>Lihat Semua</span>
                            <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-2xl bg-surface-subtle border border-border-clean/40 flex flex-col gap-1">
                            <span class="text-[11px] font-bold text-primary uppercase tracking-wide">Tahap 1</span>
                            <span class="text-sm font-bold text-on-surface">Klaim &amp; Argumen</span>
                            <span class="text-xs text-text-muted">Selesai (Skor 92)</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-primary-light border border-primary-container/30 flex flex-col gap-1">
                            <span class="text-[11px] font-bold text-primary uppercase tracking-wide">Tahap 2 • Aktif</span>
                            <span class="text-sm font-bold text-on-surface">Mekanisme &amp; Dampak</span>
                            <span class="text-xs text-primary font-semibold">Siap Latihan</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-surface-subtle/60 border border-border-clean/30 flex flex-col gap-1 opacity-75">
                            <span class="text-[11px] font-bold text-text-muted uppercase tracking-wide">Tahap 3</span>
                            <span class="text-sm font-bold text-on-surface">Rebuttal &amp; Clash</span>
                            <span class="text-xs text-text-muted">Terkunci</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-text-muted pt-2 border-t border-border-clean/50">
                        <span>Format Aktif: British Parliamentary (BP)</span>
                        <Link href="/practice" class="text-primary font-bold hover:underline">Mulai Pidato &rarr;</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
