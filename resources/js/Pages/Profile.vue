<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps<{
    appName: string;
}>();

const preferredFormat = ref<'BP' | 'AP'>('BP');
const preferredLanguage = ref<'id' | 'en'>('id');
const weeklyGoal = ref(5);
const enableChimes = ref(true);
const enableReducedMotion = ref(false);
const saveSuccess = ref(false);

const saveSettings = () => {
    saveSuccess.value = true;
    setTimeout(() => {
        saveSuccess.value = false;
    }, 2500);
};
</script>

<template>
    <Head title="Profil &amp; Pengaturan — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-3xl">
            <!-- Header -->
            <header class="flex flex-col gap-1">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Preferensi Akun</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">Profil &amp; Pengaturan</h1>
                <p class="text-sm text-text-muted">Atur format debat utama, bahasa pengantar, dan preferensi aksesibilitas latihan Anda.</p>
            </header>

            <!-- User Card -->
            <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 rounded-3xl bg-primary flex items-center justify-center text-white text-2xl font-bold shadow-xs">
                        <span class="material-symbols-outlined text-3xl" aria-hidden="true">person</span>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-primary-container ring-2 ring-surface-white" title="Aktif"></span>
                    </div>

                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-on-surface">Debater Pelajar</span>
                            <span class="px-2 py-0.5 rounded-full bg-primary-light text-primary text-[10px] font-extrabold uppercase">
                                Level 1
                            </span>
                        </div>
                        <span class="text-xs text-text-muted">Bergabung sejak Oktober 2026</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs font-bold text-accent-orange">🔥 5 Hari Streak</span>
                            <span class="text-xs text-text-muted">•</span>
                            <span class="text-xs font-bold text-secondary">560 XP</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end">
                    <span class="px-3 py-1.5 rounded-full bg-surface-subtle text-xs font-semibold text-text-muted border border-border-clean">
                        Mode Lokal (No Auth Required)
                    </span>
                </div>
            </div>

            <!-- Settings Form -->
            <form class="flex flex-col gap-6" @submit.prevent="saveSettings">
                <!-- Debate Format Preference -->
                <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-4">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Format Debat Utama</h2>
                        <p class="text-xs text-text-muted">Pilih format latihan yang menjadi fokus kurikulum standar Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup" aria-label="Format Debat Utama">
                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="preferredFormat === 'BP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="preferredFormat = 'BP'"
                        >
                            <span class="text-sm font-bold text-on-surface">British Parliamentary (BP)</span>
                            <span class="text-xs text-text-muted">4 Tim (OG, OO, CG, CO) • Standar WUDC / NUDC</span>
                        </button>

                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="preferredFormat === 'AP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="preferredFormat = 'AP'"
                        >
                            <span class="text-sm font-bold text-on-surface">Asian Parliamentary (AP)</span>
                            <span class="text-xs text-text-muted">2 Tim (Gov vs Opp) • 3 Pembicara + Reply • UADC / KDMI</span>
                        </button>
                    </div>
                </div>

                <!-- Language & Speech Target -->
                <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-4">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Bahasa &amp; Target Mingguan</h2>
                        <p class="text-xs text-text-muted">Atur bahasa default umpan balik AI dan komitmen latihan Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-text-muted uppercase tracking-wider">Bahasa Default</label>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="flex-1 py-2.5 rounded-2xl border text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                                    :class="preferredLanguage === 'id' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                                    @click="preferredLanguage = 'id'"
                                >
                                    Bahasa Indonesia
                                </button>
                                <button
                                    type="button"
                                    class="flex-1 py-2.5 rounded-2xl border text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                                    :class="preferredLanguage === 'en' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                                    @click="preferredLanguage = 'en'"
                                >
                                    English
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="weekly-goal-select" class="text-xs font-bold text-text-muted uppercase tracking-wider">
                                Target Pidato Per Minggu
                            </label>
                            <select
                                id="weekly-goal-select"
                                v-model="weeklyGoal"
                                class="px-4 py-2.5 rounded-2xl bg-surface-subtle border border-border-clean text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                            >
                                <option :value="3">3 Pidato / Minggu (Santai)</option>
                                <option :value="5">5 Pidato / Minggu (Rekomendasi)</option>
                                <option :value="7">7 Pidato / Minggu (Intensif)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Accessibility & Audio Settings -->
                <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col gap-4">
                    <div>
                        <h2 class="text-base font-bold text-on-surface">Audio &amp; Aksesibilitas</h2>
                        <p class="text-xs text-text-muted">Sesuaikan kenyamanan sensorik dan navigasi saat latihan pidato.</p>
                    </div>

                    <div class="flex flex-col divide-y divide-border-clean/50">
                        <label class="py-3 flex items-center justify-between cursor-pointer min-h-[44px]">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Bel Suara POI (Menit 1 &amp; 6)</span>
                                <span class="text-xs text-text-muted">Membunyikan lonceng sinyal dimulainya periode interupsi / POI</span>
                            </div>
                            <input
                                v-model="enableChimes"
                                type="checkbox"
                                class="w-5 h-5 rounded-md text-primary-container focus:ring-primary-container cursor-pointer"
                            />
                        </label>

                        <label class="py-3 flex items-center justify-between cursor-pointer min-h-[44px]">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Kurangi Animasi (Reduced Motion)</span>
                                <span class="text-xs text-text-muted">Menonaktifkan efek transisi dan animasi berlebih di seluruh halaman</span>
                            </div>
                            <input
                                v-model="enableReducedMotion"
                                type="checkbox"
                                class="w-5 h-5 rounded-md text-primary-container focus:ring-primary-container cursor-pointer"
                            />
                        </label>
                    </div>
                </div>

                <!-- Action Button & Notification -->
                <div class="flex items-center justify-between pt-2">
                    <span v-if="saveSuccess" class="text-xs font-bold text-primary flex items-center gap-1.5 animate-bounce">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Preferensi berhasil disimpan!
                    </span>
                    <span v-else></span>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-xs hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                    >
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
