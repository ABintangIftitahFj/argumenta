<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps<{
    appName: string;
}>();

const selectedFormat = ref<'BP' | 'AP'>('BP');
const selectedLanguage = ref<'id' | 'en'>('id');
const motion = ref('Dewan ini akan mewajibkan transparansi penuh atas penggunaan model AI generatif di sektor publik.');
</script>

<template>
    <Head title="Setup Latihan — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-3xl">
            <header class="flex flex-col gap-1">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Setup Latihan Pidato</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">Pilih Format &amp; Posisi Debat</h1>
                <p class="text-sm text-text-muted">Konfigurasikan sesi latihan pidato mandiri Anda sebelum mulai merekam suara.</p>
            </header>

            <div class="rounded-3xl bg-surface-white p-6 sm:p-8 shadow-sm border border-border-clean/60 flex flex-col gap-6">
                <!-- Format Toggle -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-text-muted">Format Debat</label>
                    <div class="grid grid-cols-2 gap-3" role="radiogroup">
                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="selectedFormat === 'BP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="selectedFormat = 'BP'"
                        >
                            <span class="text-sm font-bold text-on-surface">British Parliamentary (BP)</span>
                            <span class="text-xs text-text-muted">4 Tim • 8 Pembicara • Durasi 7 Menit</span>
                        </button>
                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="selectedFormat === 'AP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="selectedFormat = 'AP'"
                        >
                            <span class="text-sm font-bold text-on-surface">Asian Parliamentary (AP)</span>
                            <span class="text-xs text-text-muted">2 Tim • 3 Pembicara + Reply • Durasi 7 Menit</span>
                        </button>
                    </div>
                </div>

                <!-- Language Toggle -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-text-muted">Bahasa Pidato</label>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold border transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="selectedLanguage === 'id' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                            @click="selectedLanguage = 'id'"
                        >
                            Bahasa Indonesia
                        </button>
                        <button
                            type="button"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold border transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="selectedLanguage === 'en' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                            @click="selectedLanguage = 'en'"
                        >
                            English
                        </button>
                    </div>
                </div>

                <!-- Motion Input Card -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-text-muted">Mosi Debat</label>
                    <div class="p-4 rounded-2xl bg-surface-subtle border border-border-clean flex flex-col gap-2">
                        <p class="text-sm font-semibold text-on-surface leading-relaxed">"{{ motion }}"</p>
                        <div class="flex items-center gap-2 text-xs text-text-muted">
                            <span class="material-symbols-outlined text-sm text-primary" aria-hidden="true">auto_awesome</span>
                            <span>Mosi Terpilih dari Katalog Rekomendasi Hari Ini</span>
                        </div>
                    </div>
                </div>

                <!-- Role Selection Preview -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-text-muted">Posisi Pembicara</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button
                            type="button"
                            class="p-3 rounded-2xl border text-center text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container bg-primary-light border-primary-container text-primary"
                        >
                            Prime Minister (PM)
                        </button>
                        <button
                            type="button"
                            class="p-3 rounded-2xl border text-center text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container border-border-clean hover:bg-surface-subtle text-on-surface"
                        >
                            Leader of Opp (LO)
                        </button>
                        <button
                            type="button"
                            class="p-3 rounded-2xl border text-center text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container border-border-clean hover:bg-surface-subtle text-on-surface"
                        >
                            Deputy PM (DPM)
                        </button>
                        <button
                            type="button"
                            class="p-3 rounded-2xl border text-center text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container border-border-clean hover:bg-surface-subtle text-on-surface"
                        >
                            Deputy LO (DLO)
                        </button>
                    </div>
                </div>

                <!-- Submit CTA -->
                <div class="pt-4 border-t border-border-clean/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-text-muted">
                        <span class="material-symbols-outlined text-base text-primary">timer</span>
                        <span>Durasi Pidato Standar: 7 Menit (Grace period 15 detik)</span>
                    </div>

                    <button
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                    >
                        <span>Mulai Persiapan Pidato</span>
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
