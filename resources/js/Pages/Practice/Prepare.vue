<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onUnmounted, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

interface SessionData {
    id: number;
    format: 'BP' | 'AP';
    motion_text: string;
    role: string;
    language: string;
    difficulty: string;
    duration_seconds: number;
    poi_allowed: boolean;
    poi_start_second: number;
    poi_end_second: number;
    ai_coaching_enabled: boolean;
}

interface RuleData {
    duration_seconds: number;
    poi_allowed: boolean;
    poi_start_second: number;
    poi_end_second: number;
    role_name: string;
    team: string;
    side: string;
    brief: string;
}

const props = defineProps<{
    appName: string;
    session: SessionData;
    rules: RuleData;
}>();

// 15-minute Case Prep Countdown Timer
const prepSecondsLeft = ref(15 * 60);
const isTimerRunning = ref(false);
let timerInterval: any = null;

const formattedPrepTime = computed(() => {
    const mins = Math.floor(prepSecondsLeft.value / 60);
    const secs = prepSecondsLeft.value % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
});

const startTimer = () => {
    if (isTimerRunning.value) return;
    isTimerRunning.value = true;
    timerInterval = setInterval(() => {
        if (prepSecondsLeft.value > 0) {
            prepSecondsLeft.value--;
        } else {
            pauseTimer();
        }
    }, 1000);
};

const pauseTimer = () => {
    isTimerRunning.value = false;
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
};

const resetTimer = () => {
    pauseTimer();
    prepSecondsLeft.value = 15 * 60;
};

const addMinute = () => {
    prepSecondsLeft.value += 60;
};

onUnmounted(() => {
    pauseTimer();
});

const userNotes = ref('');
</script>

<template>
    <Head title="Persiapan Kasus Pidato — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-4xl mx-auto">
            <!-- Header -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Link
                            href="/practice"
                            class="inline-flex items-center gap-1 text-xs font-bold text-text-muted hover:text-primary transition-all min-h-[36px]"
                        >
                            <span class="material-symbols-outlined text-sm">arrow_back</span>
                            <span>Kembali ke Setup</span>
                        </Link>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">
                        Persiapan Kasus (Case Prep)
                    </h1>
                    <p class="text-sm text-text-muted">
                        Susun struktur AREL Anda sebelum memulai sesi rekaman pidato mandiri.
                    </p>
                </div>

                <!-- 15-Minute Prep Timer Box -->
                <div class="p-4 rounded-3xl bg-surface-white border border-border-clean/60 shadow-xs flex flex-col items-center gap-2 min-w-[200px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-text-muted">Waktu Prep Standar (15m)</span>
                    <span class="text-3xl font-extrabold text-on-surface font-mono tracking-tight">{{ formattedPrepTime }}</span>

                    <div class="flex items-center gap-1.5">
                        <button
                            v-if="!isTimerRunning"
                            type="button"
                            class="p-2 rounded-xl bg-primary-container text-white text-xs font-bold hover:bg-primary transition-all min-h-[44px] min-w-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            title="Mulai Timer Prep"
                            @click="startTimer"
                        >
                            <span class="material-symbols-outlined text-lg">play_arrow</span>
                        </button>
                        <button
                            v-else
                            type="button"
                            class="p-2 rounded-xl bg-surface-subtle text-on-surface text-xs font-bold hover:bg-surface-dim transition-all min-h-[44px] min-w-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            title="Jeda Timer Prep"
                            @click="pauseTimer"
                        >
                            <span class="material-symbols-outlined text-lg">pause</span>
                        </button>

                        <button
                            type="button"
                            class="p-2 rounded-xl bg-surface-subtle text-text-muted text-xs font-bold hover:bg-surface-dim hover:text-on-surface transition-all min-h-[44px] min-w-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            title="Reset Timer"
                            @click="resetTimer"
                        >
                            <span class="material-symbols-outlined text-lg">replay</span>
                        </button>

                        <button
                            type="button"
                            class="px-2.5 py-2 rounded-xl bg-surface-subtle text-xs font-bold text-text-muted hover:bg-surface-dim hover:text-on-surface transition-all min-h-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            title="Tambah 1 Menit"
                            @click="addMinute"
                        >
                            +1m
                        </button>
                    </div>
                </div>
            </header>

            <!-- Motion & Role Hero Card -->
            <section class="p-6 sm:p-8 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col gap-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-secondary/15 text-secondary text-xs font-extrabold uppercase">
                            {{ session.format }} • {{ rules.team }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-primary-light text-primary text-xs font-extrabold uppercase">
                            {{ rules.role_name }} ({{ session.role }})
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-text-muted">
                        <span class="flex items-center gap-1 font-semibold text-on-surface">
                            <span class="material-symbols-outlined text-sm text-primary">timer</span>
                            {{ rules.duration_seconds / 60 }} Menit
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1 font-semibold text-on-surface">
                            <span class="material-symbols-outlined text-sm text-primary">notifications_active</span>
                            {{ rules.poi_allowed ? 'POI Aktif (1m-6m)' : 'POI Dilarang' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 pt-2">
                    <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Mosi Debat:</span>
                    <h2 class="text-xl sm:text-2xl font-bold text-on-surface leading-snug">
                        "{{ session.motion_text }}"
                    </h2>
                </div>

                <!-- Role Briefing Box -->
                <div class="p-4 rounded-2xl bg-surface-subtle border border-border-clean/60 flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-on-surface">
                        <span class="material-symbols-outlined text-base text-primary">assignment</span>
                        <span>Beban Tugas Pembicara (Role Brief):</span>
                    </div>
                    <p class="text-xs sm:text-sm text-on-surface-variant leading-relaxed">
                        {{ rules.brief }}
                    </p>
                </div>
            </section>

            <!-- 2-Column Grid: Notes/Scratchpad & AI Prep Assistant -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Case Scratchpad -->
                <div class="lg:col-span-2 p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-on-surface">Catatan Draf Argumen (Scratchpad)</h3>
                        <span class="text-xs text-text-muted">Tersimpan lokal di sesi ini</span>
                    </div>

                    <p class="text-xs text-text-muted">
                        Tips struktur AREL: <strong>A</strong>ssertion (Klaim) &rarr; <strong>R</strong>easoning (Mengapa benar) &rarr; <strong>E</strong>vidence (Contoh &amp; data) &rarr; <strong>L</strong>inkback (Relevansi ke mosi).
                    </p>

                    <textarea
                        v-model="userNotes"
                        rows="8"
                        placeholder="Contoh catatan:&#10;1. Argumen 1: Urgensi perlindungan data konsumen...&#10;2. Mekanisme: Audit independen berkala...&#10;3. Weighing: Dampak jangka panjang lebih bernilai dibanding beban kepatuhan awal..."
                        class="w-full p-4 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary-container resize-none min-h-[140px]"
                    ></textarea>
                </div>

                <!-- AI Coach Mini Preview -->
                <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col justify-between gap-4">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">smart_toy</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-on-surface">Coach Kiko</h3>
                                <span class="text-[10px] text-primary font-bold uppercase">Panduan Posisi</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-primary-light/50 border border-primary-container/20 text-xs text-on-surface leading-relaxed flex flex-col gap-2">
                            <span>"Ingat, kunci utama posisi <strong>{{ session.role }}</strong> adalah menjawab <em>'Mengapa dunia tim Anda lebih baik?'</em> secara komparatif."</span>
                        </div>
                    </div>

                    <Link
                        href="/coach"
                        class="inline-flex items-center justify-center gap-1.5 p-3 rounded-2xl bg-surface-subtle text-on-surface text-xs font-bold hover:bg-surface-dim transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                    >
                        <span class="material-symbols-outlined text-base text-primary">chat</span>
                        <span>Konsultasi Mosi dengan Kiko</span>
                    </Link>
                </div>
            </div>

            <!-- Ready CTA -->
            <div class="p-6 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-accent-orange/15 text-accent-orange flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl">mic</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-sm font-bold text-on-surface">Persiapan Anda Sudah Matang?</h3>
                        <p class="text-xs text-text-muted">Masuk ke ruang rekaman pidato mandiri dan mulai berbicara.</p>
                    </div>
                </div>

                <Link
                    :href="`/practice/${session.id}/record`"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                >
                    <span class="material-symbols-outlined text-lg" aria-hidden="true">fiber_manual_record</span>
                    <span>Mulai Rekam Pidato Sekarang</span>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
