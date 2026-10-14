<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

interface Issue {
    id: number;
    category: string;
    title: string;
    original_quote: string | null;
    suggested_fix: string | null;
    explanation: string;
    start_time_seconds: number | null;
    end_time_seconds: number | null;
    severity: string;
}

interface Segment {
    id: number;
    segment_order: number;
    start_time_seconds: number;
    end_time_seconds: number;
    text: string;
    confidence: number;
    is_uncertain: boolean;
}

interface Transcript {
    id: number;
    raw_text: string;
    user_edited_text: string | null;
    effective_text: string;
    language: string;
    confidence_score: number;
    words_count: number;
    words_per_minute: number;
    has_uncertainty: boolean;
    audio_quality_warning: string | null;
    asr_provider: string;
    segments: Segment[];
}

interface Evaluation {
    id: number;
    overall_score: number;
    matter_score: number;
    manner_score: number;
    method_score: number;
    language_score: number | null;
    summary: string;
    strengths: string[];
    improvements: string[];
    next_drill_recommendation: string;
    model_version: string;
    rubric_version: string;
    prompt_version: string;
    issues: Issue[];
}

interface Session {
    id: number;
    format: string;
    role: string;
    motion_text: string;
    language: string;
    difficulty: string;
    duration_seconds: number;
    status: string;
    notes: string | null;
}

const props = defineProps<{
    session: Session;
    evaluation: Evaluation;
    transcript: Transcript;
    audioUrl: string | null;
}>();

// Audio player state & syncing
const audioPlayer = ref<HTMLAudioElement | null>(null);
const currentTime = ref(0);
const isPlaying = ref(false);

const onTimeUpdate = () => {
    if (audioPlayer.value) {
        currentTime.value = audioPlayer.value.currentTime;
    }
};

const onPlay = () => {
    isPlaying.value = true;
};

const onPause = () => {
    isPlaying.value = false;
};

const seekToTime = (seconds: number) => {
    if (audioPlayer.value) {
        audioPlayer.value.currentTime = seconds;
        audioPlayer.value.play().catch(() => {});
    }
};

const isSegmentActive = (segment: Segment) => {
    return currentTime.value >= segment.start_time_seconds && currentTime.value <= segment.end_time_seconds;
};

// Transcript user correction mode
const isEditingTranscript = ref(false);
const editedText = ref(props.transcript.effective_text);
const isSavingCorrection = ref(false);
const saveSuccessMessage = ref<string | null>(null);

const saveTranscriptCorrection = () => {
    isSavingCorrection.value = true;
    router.patch(
        `/practice/${props.session.id}/transcript`,
        { edited_text: editedText.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                isEditingTranscript.value = false;
                saveSuccessMessage.value = 'Transkrip telah berhasil diperbarui!';
                setTimeout(() => {
                    saveSuccessMessage.value = null;
                }, 4000);
            },
            onFinish: () => {
                isSavingCorrection.value = false;
            },
        }
    );
};

// Active tab for mobile navigation
const activeTab = ref<'scorecard' | 'transcript' | 'issues'>('scorecard');

const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
};
</script>

<template>
    <Head :title="`Hasil Evaluasi — ${session.role} (${session.format})`" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-5xl mx-auto pb-12">
            <!-- Header bar with breadcrumbs and actions -->
            <header class="flex flex-wrap items-center justify-between gap-4">
                <Link
                    href="/practice"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-text-muted hover:text-primary transition-all min-h-[44px]"
                >
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    <span>Kembali ke Latihan</span>
                </Link>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-primary-light text-primary">
                        {{ session.format }} • {{ session.role }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-surface-subtle text-text-muted">
                        {{ session.language.toUpperCase() }}
                    </span>
                </div>
            </header>

            <!-- Motion & Brief Banner -->
            <div class="p-5 md:p-6 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-3">
                <div class="flex items-center justify-between text-xs text-text-muted font-bold">
                    <span>Mosi Perdebatan</span>
                    <span class="capitalize">Level: {{ session.difficulty }}</span>
                </div>
                <h1 class="text-lg md:text-xl font-black text-on-surface leading-snug">
                    "{{ session.motion_text }}"
                </h1>
            </div>

            <!-- Tab Navigation for Mobile -->
            <div class="flex md:hidden border-b border-border-clean gap-2">
                <button
                    type="button"
                    class="flex-1 py-3 text-xs font-black border-b-2 transition-all min-h-[44px]"
                    :class="activeTab === 'scorecard' ? 'border-primary text-primary' : 'border-transparent text-text-muted'"
                    @click="activeTab = 'scorecard'"
                >
                    Kartu Skor & Kritik
                </button>
                <button
                    type="button"
                    class="flex-1 py-3 text-xs font-black border-b-2 transition-all min-h-[44px]"
                    :class="activeTab === 'transcript' ? 'border-primary text-primary' : 'border-transparent text-text-muted'"
                    @click="activeTab = 'transcript'"
                >
                    Transkrip & Audio
                </button>
                <button
                    type="button"
                    class="flex-1 py-3 text-xs font-black border-b-2 transition-all min-h-[44px]"
                    :class="activeTab === 'issues' ? 'border-primary text-primary' : 'border-transparent text-text-muted'"
                    @click="activeTab = 'issues'"
                >
                    Catatan Sanggahan ({{ evaluation.issues.length }})
                </button>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Column Left: Scorecard & Core Feedback (cols 12 on mobile, cols 7 on lg) -->
                <div
                    class="lg:col-span-7 flex flex-col gap-6"
                    :class="{ 'hidden md:flex': activeTab !== 'scorecard' && activeTab !== 'issues' }"
                >
                    <!-- Scorecard Hero Card -->
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-primary-container to-teal-800 text-white shadow-lg flex flex-col gap-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-wider font-extrabold text-white/80">Debate Speaker Score</span>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <span class="text-4xl md:text-5xl font-black">{{ evaluation.overall_score }}</span>
                                    <span class="text-base font-bold text-white/70">/ 100</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/20 text-xs font-black backdrop-blur-sm">
                                    <span class="material-symbols-outlined text-sm">verified</span>
                                    <span>{{ evaluation.rubric_version.toUpperCase() }}</span>
                                </span>
                                <div class="text-[11px] text-white/70 mt-1">Model: {{ evaluation.model_version }}</div>
                            </div>
                        </div>

                        <!-- Rubric Dimension Scores -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-4 border-t border-white/20">
                            <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm flex flex-col">
                                <span class="text-[11px] font-bold text-white/80 uppercase">Matter (Isi)</span>
                                <span class="text-lg font-black mt-0.5">{{ evaluation.matter_score }}</span>
                                <span class="text-[10px] text-white/70">Argumen & Bukti</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm flex flex-col">
                                <span class="text-[11px] font-bold text-white/80 uppercase">Manner (Gaya)</span>
                                <span class="text-lg font-black mt-0.5">{{ evaluation.manner_score }}</span>
                                <span class="text-[10px] text-white/70">Retorika & Tempo</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm flex flex-col">
                                <span class="text-[11px] font-bold text-white/80 uppercase">Method (Struktur)</span>
                                <span class="text-lg font-black mt-0.5">{{ evaluation.method_score }}</span>
                                <span class="text-[10px] text-white/70">Signposting & Alur</span>
                            </div>

                            <div class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm flex flex-col border border-white/20">
                                <span class="text-[11px] font-bold text-amber-200 uppercase">Bahasa (Terpisah)</span>
                                <span class="text-lg font-black mt-0.5">{{ evaluation.language_score ?? 'N/A' }}</span>
                                <span class="text-[10px] text-white/70">Tanpa bias aksen</span>
                            </div>
                        </div>

                        <!-- Adjudicator Summary -->
                        <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-sm text-xs leading-relaxed text-white/95">
                            <div class="font-extrabold text-white mb-1 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">format_quote</span>
                                <span>Ringkasan Keputusan Juri AI</span>
                            </div>
                            <p>{{ evaluation.summary }}</p>
                        </div>
                    </div>

                    <!-- 3 Strengths & 3 Improvements -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 3 Strengths -->
                        <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-primary font-black text-sm">
                                <span class="material-symbols-outlined text-xl">check_circle</span>
                                <span>3 Kekuatan Utama</span>
                            </div>
                            <ul class="flex flex-col gap-2.5">
                                <li
                                    v-for="(strength, idx) in evaluation.strengths"
                                    :key="idx"
                                    class="p-3 rounded-2xl bg-primary-light/50 border border-primary/20 text-xs text-primary-dark font-medium leading-relaxed flex items-start gap-2"
                                >
                                    <span class="font-black text-primary text-xs shrink-0">{{ idx + 1 }}.</span>
                                    <span>{{ strength }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- 3 Priority Improvements -->
                        <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-accent-orange font-black text-sm">
                                <span class="material-symbols-outlined text-xl">flag</span>
                                <span>3 Prioritas Perbaikan</span>
                            </div>
                            <ul class="flex flex-col gap-2.5">
                                <li
                                    v-for="(improvement, idx) in evaluation.improvements"
                                    :key="idx"
                                    class="p-3 rounded-2xl bg-accent-orange/10 border border-accent-orange/20 text-xs text-on-surface font-medium leading-relaxed flex items-start gap-2"
                                >
                                    <span class="font-black text-accent-orange text-xs shrink-0">{{ idx + 1 }}.</span>
                                    <span>{{ improvement }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Next Drill Recommendation Card -->
                    <div class="p-5 rounded-3xl bg-surface-lowest border-2 border-primary/30 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">school</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-black uppercase text-primary tracking-wider">Rekomendasi Latihan Selanjutnya</span>
                                <h3 class="text-xs font-bold text-on-surface mt-0.5">
                                    {{ evaluation.next_drill_recommendation }}
                                </h3>
                            </div>
                        </div>

                        <Link
                            href="/learn"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-primary-container text-white font-bold text-xs shadow-sm hover:bg-primary transition-all min-h-[44px] shrink-0"
                        >
                            <span>Buka Modul Kurikulum</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </Link>
                    </div>

                    <!-- Issues & Specific Argumentation Feedback -->
                    <div
                        v-if="evaluation.issues.length > 0"
                        class="flex flex-col gap-3"
                        :class="{ 'block': activeTab === 'issues' || activeTab === 'scorecard' }"
                    >
                        <h2 class="text-sm font-black text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg text-primary">psychology</span>
                            <span>Anotasi Kritik Argumen & Lapisan AREL ({{ evaluation.issues.length }})</span>
                        </h2>

                        <div class="flex flex-col gap-3">
                            <div
                                v-for="issue in evaluation.issues"
                                :key="issue.id"
                                class="p-4 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-2.5"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-accent-orange/15 text-accent-orange">
                                            {{ issue.category }}
                                        </span>
                                        <span class="text-xs font-black text-on-surface">{{ issue.title }}</span>
                                    </div>

                                    <button
                                        v-if="issue.start_time_seconds !== null"
                                        type="button"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-primary hover:underline min-h-[36px]"
                                        @click="seekToTime(issue.start_time_seconds)"
                                    >
                                        <span class="material-symbols-outlined text-sm">play_circle</span>
                                        <span>{{ formatTime(issue.start_time_seconds) }}</span>
                                    </button>
                                </div>

                                <div v-if="issue.original_quote" class="p-2.5 rounded-xl bg-surface-subtle border-l-4 border-accent-orange text-xs text-text-muted italic">
                                    "{{ issue.original_quote }}"
                                </div>

                                <p class="text-xs text-text-muted leading-relaxed">{{ issue.explanation }}</p>

                                <div v-if="issue.suggested_fix" class="p-3 rounded-2xl bg-primary-light/40 border border-primary/20 text-xs text-primary-dark">
                                    <strong class="font-black">Usulan Perbaikan:</strong> {{ issue.suggested_fix }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column Right: Interactive Transcript & Audio Replay (cols 12 on mobile, cols 5 on lg) -->
                <div
                    class="lg:col-span-5 flex flex-col gap-4"
                    :class="{ 'hidden md:flex': activeTab !== 'transcript' }"
                >
                    <!-- Audio Player Box -->
                    <div class="p-5 rounded-3xl bg-surface-lowest border border-border-clean/80 shadow-sm flex flex-col gap-3 sticky top-4">
                        <div class="flex items-center justify-between text-xs font-bold text-on-surface">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-primary text-base">graphic_eq</span>
                                <span>Replay Audio Pidato</span>
                            </span>
                            <span class="text-text-muted">{{ formatTime(currentTime) }} / {{ formatTime(session.duration_seconds) }}</span>
                        </div>

                        <audio
                            v-if="audioUrl"
                            ref="audioPlayer"
                            :src="audioUrl"
                            controls
                            class="w-full h-11 rounded-2xl focus:outline-none"
                            @timeupdate="onTimeUpdate"
                            @play="onPlay"
                            @pause="onPause"
                        ></audio>

                        <div v-else class="p-3 rounded-2xl bg-surface-subtle text-xs text-text-muted text-center">
                            Audio privat tidak tersedia atau telah dihapus.
                        </div>

                        <!-- Audio Quality / Uncertainty Notice -->
                        <div
                            v-if="transcript.has_uncertainty"
                            class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-800 flex items-start gap-2"
                        >
                            <span class="material-symbols-outlined text-base shrink-0 text-amber-600">info</span>
                            <span>{{ transcript.audio_quality_warning || 'Terdapat penanda kata ragu/tidak pasti pada rekaman.' }}</span>
                        </div>

                        <!-- Transcript Header with Edit Toggle -->
                        <div class="flex items-center justify-between pt-2 border-t border-border-clean">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-on-surface">Transkrip STT</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-surface-subtle text-text-muted font-bold">
                                    {{ transcript.words_count }} kata • {{ transcript.words_per_minute }} WPM
                                </span>
                            </div>

                            <button
                                type="button"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold transition-all min-h-[36px]"
                                :class="isEditingTranscript ? 'bg-error/15 text-error' : 'bg-primary-light text-primary hover:bg-primary-light/80'"
                                @click="isEditingTranscript = !isEditingTranscript"
                            >
                                <span class="material-symbols-outlined text-sm">{{ isEditingTranscript ? 'close' : 'edit' }}</span>
                                <span>{{ isEditingTranscript ? 'Batal' : 'Koreksi Transkrip' }}</span>
                            </button>
                        </div>

                        <!-- Success message after correction -->
                        <div v-if="saveSuccessMessage" class="p-2.5 rounded-xl bg-primary-light text-primary text-xs font-bold">
                            {{ saveSuccessMessage }}
                        </div>

                        <!-- User Correction Edit Mode -->
                        <div v-if="isEditingTranscript" class="flex flex-col gap-3">
                            <textarea
                                v-model="editedText"
                                rows="8"
                                class="w-full p-3.5 rounded-2xl border border-border-clean bg-surface-lowest text-xs text-on-surface focus:outline-none focus:ring-2 focus:ring-primary leading-relaxed"
                                placeholder="Koreksi kata yang salah transkrip..."
                            ></textarea>

                            <button
                                type="button"
                                :disabled="isSavingCorrection"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold hover:bg-primary transition-all min-h-[44px] disabled:opacity-50"
                                @click="saveTranscriptCorrection"
                            >
                                <span v-if="isSavingCorrection" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                                <span v-else class="material-symbols-outlined text-sm">save</span>
                                <span>Simpan Koreksi Transkrip</span>
                            </button>
                        </div>

                        <!-- Interactive Segment-by-Segment View -->
                        <div v-else class="flex flex-col gap-2 max-h-[500px] overflow-y-auto pr-1">
                            <div
                                v-for="segment in transcript.segments"
                                :key="segment.id"
                                class="p-3 rounded-2xl border text-xs leading-relaxed transition-all cursor-pointer select-none"
                                :class="[
                                    isSegmentActive(segment)
                                        ? 'bg-primary-light/70 border-primary font-bold shadow-sm'
                                        : 'bg-surface-subtle/60 border-border-clean/50 hover:bg-surface-subtle hover:border-border-clean'
                                ]"
                                @click="seekToTime(segment.start_time_seconds)"
                            >
                                <div class="flex items-center justify-between text-[10px] text-text-muted mb-1">
                                    <span class="font-bold">{{ formatTime(segment.start_time_seconds) }} - {{ formatTime(segment.end_time_seconds) }}</span>
                                    <span
                                        v-if="segment.is_uncertain"
                                        class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-700 font-extrabold flex items-center gap-1"
                                        title="Penanda ketidakpastian ASR (kata dengan akurasi rendah)"
                                    >
                                        <span class="material-symbols-outlined text-[10px]">warning</span>
                                        <span>Ragu</span>
                                    </span>
                                </div>
                                <p :class="{ 'underline decoration-amber-500 decoration-wavy': segment.is_uncertain }">
                                    {{ segment.text }}
                                </p>
                            </div>

                            <!-- If user edited text is active, show badge -->
                            <div v-if="transcript.user_edited_text" class="p-2 text-[11px] text-primary font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                <span>Transkrip ini telah diverifikasi & dikoreksi oleh pengguna.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
