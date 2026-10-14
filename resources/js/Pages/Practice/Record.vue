<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';
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
    status: string;
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

interface RecordingData {
    id: number;
    duration_seconds: number;
    file_size_bytes: number;
    mime_type: string;
    upload_status: string;
    stream_url: string;
}

const props = defineProps<{
    appName: string;
    session: SessionData;
    rules: RuleData;
    latestRecording?: RecordingData | null;
}>();

// Recording & Audio State
type RecordState = 'idle' | 'recording' | 'paused' | 'stopped' | 'uploading' | 'uploaded' | 'failed';

const recordState = ref<RecordState>(props.latestRecording ? 'uploaded' : 'idle');
const audioBlob = ref<Blob | null>(null);
const audioUrl = ref<string | null>(props.latestRecording?.stream_url || null);
const uploadErrorMessage = ref<string | null>(null);
const uploadProgress = ref<number>(0);

let mediaStream: MediaStream | null = null;
let mediaRecorder: MediaRecorder | null = null;
let recordedChunks: Blob[] = [];

// Audio Visualizer
let audioContext: AudioContext | null = null;
let analyser: AnalyserNode | null = null;
let animFrameId: number | null = null;
const volumeLevels = ref<number[]>([15, 20, 25, 30, 20, 15, 25, 35, 20]);

// Speech Timer
const elapsedSeconds = ref(props.latestRecording?.duration_seconds || 0);
let speechInterval: any = null;

const formattedTime = computed(() => {
    const mins = Math.floor(elapsedSeconds.value / 60);
    const secs = elapsedSeconds.value % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
});

const isPoiPeriod = computed(() => {
    if (!props.rules.poi_allowed) return false;
    return (
        elapsedSeconds.value >= props.rules.poi_start_second &&
        elapsedSeconds.value <= props.rules.poi_end_second
    );
});

const isOvertime = computed(() => {
    return elapsedSeconds.value > props.rules.duration_seconds;
});

const isGracePeriod = computed(() => {
    return (
        elapsedSeconds.value > props.rules.duration_seconds &&
        elapsedSeconds.value <= props.rules.duration_seconds + 15
    );
});

// Setup Web Audio Visualizer
const initAudioVisualizer = (stream: MediaStream) => {
    try {
        const AudioCtx = window.AudioContext || (window as any).webkitAudioContext;
        if (!AudioCtx) return;
        audioContext = new AudioCtx();
        const source = audioContext.createMediaStreamSource(stream);
        analyser = audioContext.createAnalyser();
        analyser.fftSize = 32;
        source.connect(analyser);

        const dataArray = new Uint8Array(analyser.frequencyBinCount);

        const updateMeter = () => {
            if (!analyser || recordState.value !== 'recording') return;
            analyser.getByteFrequencyData(dataArray);

            // Update 9 bars proportionally
            const bars = [];
            for (let i = 0; i < 9; i++) {
                const val = dataArray[i % dataArray.length] || 0;
                bars.push(Math.max(12, Math.min(100, Math.round((val / 255) * 100))));
            }
            volumeLevels.value = bars;
            animFrameId = requestAnimationFrame(updateMeter);
        };
        updateMeter();
    } catch (e) {
        // Fallback gracefully if Web Audio API blocked
        console.warn('AudioContext not available:', e);
    }
};

const cleanupAudio = () => {
    if (speechInterval) {
        clearInterval(speechInterval);
        speechInterval = null;
    }
    if (animFrameId) {
        cancelAnimationFrame(animFrameId);
        animFrameId = null;
    }
    if (audioContext) {
        audioContext.close();
        audioContext = null;
    }
    if (mediaStream) {
        mediaStream.getTracks().forEach((track) => track.stop());
        mediaStream = null;
    }
};

onUnmounted(() => {
    cleanupAudio();
    if (audioUrl.value && !props.latestRecording) {
        URL.revokeObjectURL(audioUrl.value);
    }
});

const startRecording = async () => {
    try {
        recordedChunks = [];
        uploadErrorMessage.value = null;

        mediaStream = await navigator.mediaDevices.getUserMedia({
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
            },
        });

        initAudioVisualizer(mediaStream);

        // Try supported audio MIME types
        let mimeType = 'audio/webm';
        if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
            mimeType = 'audio/webm;codecs=opus';
        } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
            mimeType = 'audio/mp4';
        } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
            mimeType = 'audio/ogg';
        }

        mediaRecorder = new MediaRecorder(mediaStream, { mimeType });

        mediaRecorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) {
                recordedChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = () => {
            audioBlob.value = new Blob(recordedChunks, { type: mimeType });
            if (audioUrl.value && !props.latestRecording) {
                URL.revokeObjectURL(audioUrl.value);
            }
            audioUrl.value = URL.createObjectURL(audioBlob.value);
            recordState.value = 'stopped';
        };

        mediaRecorder.start(250); // Emit chunk every 250ms
        recordState.value = 'recording';
        elapsedSeconds.value = 0;

        speechInterval = setInterval(() => {
            elapsedSeconds.value++;
        }, 1000);
    } catch (err: any) {
        console.error('Microphone access error:', err);
        uploadErrorMessage.value = 'Izin mikrofon diperlukan untuk merekam pidato. Mohon izinkan akses mikrofon di peramban Anda.';
        recordState.value = 'failed';
    }
};

const pauseRecording = () => {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.pause();
        recordState.value = 'paused';
        if (speechInterval) {
            clearInterval(speechInterval);
            speechInterval = null;
        }
    }
};

const resumeRecording = () => {
    if (mediaRecorder && mediaRecorder.state === 'paused') {
        mediaRecorder.resume();
        recordState.value = 'recording';
        speechInterval = setInterval(() => {
            elapsedSeconds.value++;
        }, 1000);
    }
};

const stopRecording = () => {
    if (mediaRecorder && (mediaRecorder.state === 'recording' || mediaRecorder.state === 'paused')) {
        mediaRecorder.stop();
        cleanupAudio();
    }
};

const resetRecording = () => {
    cleanupAudio();
    if (audioUrl.value && !props.latestRecording) {
        URL.revokeObjectURL(audioUrl.value);
    }
    audioBlob.value = null;
    audioUrl.value = null;
    elapsedSeconds.value = 0;
    recordState.value = 'idle';
};

const uploadRecording = async () => {
    if (!audioBlob.value) return;

    recordState.value = 'uploading';
    uploadProgress.value = 0;
    uploadErrorMessage.value = null;

    const formData = new FormData();
    const extension = audioBlob.value.type.includes('mp4') ? 'mp4' : audioBlob.value.type.includes('ogg') ? 'ogg' : 'webm';
    formData.append('audio', audioBlob.value, `speech.${extension}`);
    formData.append('duration_seconds', String(elapsedSeconds.value));

    try {
        await axios.post(`/practice/${props.session.id}/record`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    uploadProgress.value = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                }
            },
        });

        recordState.value = 'uploaded';
    } catch (error: any) {
        console.error('Upload failed:', error);
        uploadErrorMessage.value = error.response?.data?.message || 'Gagal mengunggah berkas rekaman audio. Silakan coba lagi.';
        recordState.value = 'failed';
    }
};
</script>

<template>
    <Head title="Perekaman Pidato — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-4xl mx-auto">
            <!-- Header -->
            <header class="flex items-center justify-between">
                <Link
                    :href="`/practice/${session.id}/prepare`"
                    class="inline-flex items-center gap-1 text-xs font-bold text-text-muted hover:text-primary transition-all min-h-[36px]"
                >
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Kembali ke Persiapan</span>
                </Link>

                <!-- Status Badge -->
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                        :class="[
                            recordState === 'recording' ? 'bg-error/15 text-error animate-pulse' :
                            recordState === 'paused' ? 'bg-accent-orange/15 text-accent-orange' :
                            recordState === 'uploaded' ? 'bg-primary-light text-primary' :
                            'bg-surface-subtle text-text-muted'
                        ]"
                    >
                        <span
                            class="w-2 h-2 rounded-full"
                            :class="[
                                recordState === 'recording' ? 'bg-error animate-ping' :
                                recordState === 'paused' ? 'bg-accent-orange' :
                                recordState === 'uploaded' ? 'bg-primary-container' :
                                'bg-text-muted'
                            ]"
                        ></span>
                        <span>
                            {{
                                recordState === 'recording' ? 'Merekam Pidato' :
                                recordState === 'paused' ? 'Pidato Dijeda' :
                                recordState === 'stopped' ? 'Menunggu Konfirmasi' :
                                recordState === 'uploading' ? 'Mengunggah Audio...' :
                                recordState === 'uploaded' ? 'Tersimpan Aman' :
                                recordState === 'failed' ? 'Gagal' : 'Siap Merekam'
                            }}
                        </span>
                    </span>
                </div>
            </header>

            <!-- Motion & Role Header Card -->
            <section class="p-6 sm:p-8 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-secondary/15 text-secondary text-xs font-extrabold uppercase">
                            {{ session.format }} • {{ rules.team }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-primary-light text-primary text-xs font-extrabold uppercase">
                            {{ session.role }}
                        </span>
                    </div>

                    <span class="text-xs font-semibold text-text-muted">
                        Batas Waktu: {{ rules.duration_seconds / 60 }} Menit (+15s grace)
                    </span>
                </div>

                <h1 class="text-lg sm:text-2xl font-extrabold text-on-surface font-serif leading-snug">
                    "{{ session.motion_text }}"
                </h1>
            </section>

            <!-- Recording Arena Card -->
            <div class="p-6 sm:p-10 rounded-3xl bg-surface-white border border-border-clean/60 shadow-sm flex flex-col items-center justify-center gap-8 text-center">
                <!-- Large Stopwatch Timer -->
                <div class="flex flex-col items-center gap-1">
                    <div
                        class="text-5xl sm:text-7xl font-mono font-extrabold tracking-tight transition-colors duration-300"
                        :class="[
                            isOvertime ? 'text-error' :
                            isPoiPeriod ? 'text-primary' :
                            'text-on-surface'
                        ]"
                    >
                        {{ formattedTime }}
                    </div>

                    <!-- POI & Overtime Status Subtitle -->
                    <div class="h-6 flex items-center justify-center">
                        <span v-if="isGracePeriod" class="text-xs font-bold text-accent-orange animate-bounce">
                            ⚠️ Masa Toleransi Pidato (+15 Detik Grace Period)
                        </span>
                        <span v-else-if="isOvertime" class="text-xs font-bold text-error">
                            🛑 Waktu Pidato Habis (Overtime)
                        </span>
                        <span v-else-if="isPoiPeriod" class="text-xs font-bold text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">notifications_active</span>
                            Periode POI Terbuka (Menit 1:00 s.d. 6:00)
                        </span>
                        <span v-else-if="rules.poi_allowed && elapsedSeconds < rules.poi_start_second" class="text-xs font-medium text-text-muted">
                            🔒 Protected Time (Belum ada POI)
                        </span>
                        <span v-else-if="rules.poi_allowed && elapsedSeconds > rules.poi_end_second" class="text-xs font-medium text-text-muted">
                            🔒 Protected Time (POI ditutup)
                        </span>
                        <span v-else class="text-xs font-medium text-text-muted">
                            Waktu Pidato Mandiri
                        </span>
                    </div>
                </div>

                <!-- Live Volume / Frequency Bars Visualizer -->
                <div class="flex items-end justify-center gap-1.5 h-16 w-full max-w-xs">
                    <div
                        v-for="(lvl, idx) in volumeLevels"
                        :key="idx"
                        class="w-3 rounded-full transition-all duration-100 ease-out"
                        :class="[
                            recordState === 'recording' ? 'bg-primary-container' :
                            recordState === 'paused' ? 'bg-accent-orange/60' :
                            'bg-surface-subtle'
                        ]"
                        :style="{ height: `${recordState === 'recording' ? lvl : 12}%` }"
                    ></div>
                </div>

                <!-- Primary Action Controls -->
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <!-- Idle State: Start Recording -->
                    <button
                        v-if="recordState === 'idle'"
                        type="button"
                        class="inline-flex items-center justify-center gap-3 px-8 py-4 rounded-3xl bg-primary-container text-white font-extrabold text-base shadow-md hover:bg-primary transition-all min-h-[48px] min-w-[200px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                        @click="startRecording"
                    >
                        <span class="material-symbols-outlined text-2xl" aria-hidden="true">fiber_manual_record</span>
                        <span>Mulai Rekam Pidato</span>
                    </button>

                    <!-- Active Recording State: Pause, Resume, Stop -->
                    <template v-if="recordState === 'recording' || recordState === 'paused'">
                        <button
                            v-if="recordState === 'recording'"
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-surface-subtle text-on-surface font-bold text-sm hover:bg-surface-dim transition-all min-h-[44px] min-w-[120px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            @click="pauseRecording"
                        >
                            <span class="material-symbols-outlined text-xl">pause</span>
                            <span>Jeda</span>
                        </button>

                        <button
                            v-else
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-accent-orange text-white font-bold text-sm shadow-xs hover:bg-accent-orange/90 transition-all min-h-[44px] min-w-[120px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            @click="resumeRecording"
                        >
                            <span class="material-symbols-outlined text-xl">play_arrow</span>
                            <span>Lanjutkan</span>
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-error text-white font-extrabold text-sm shadow-sm hover:bg-error/90 transition-all min-h-[44px] min-w-[150px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                            @click="stopRecording"
                        >
                            <span class="material-symbols-outlined text-xl">stop</span>
                            <span>Selesai Pidato</span>
                        </button>
                    </template>

                    <!-- Stopped State: Review & Upload -->
                    <template v-if="recordState === 'stopped'">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-surface-subtle text-text-muted font-bold text-xs hover:bg-surface-dim hover:text-on-surface transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            @click="resetRecording"
                        >
                            <span class="material-symbols-outlined text-lg">replay</span>
                            <span>Rekam Ulang</span>
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[180px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                            @click="uploadRecording"
                        >
                            <span class="material-symbols-outlined text-xl">cloud_upload</span>
                            <span>Unggah &amp; Simpan Pidato</span>
                        </button>
                    </template>

                    <!-- Uploading State -->
                    <div v-if="recordState === 'uploading'" class="flex flex-col items-center gap-2">
                        <div class="inline-flex items-center gap-2 text-primary font-bold text-sm">
                            <span class="material-symbols-outlined animate-spin text-xl">progress_activity</span>
                            <span>Mengunggah berkas audio privat ({{ uploadProgress }}%)...</span>
                        </div>
                    </div>

                    <!-- Uploaded State -->
                    <template v-if="recordState === 'uploaded'">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-surface-subtle text-text-muted font-bold text-xs hover:bg-surface-dim hover:text-on-surface transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            @click="resetRecording"
                        >
                            <span class="material-symbols-outlined text-lg">replay</span>
                            <span>Rekam Versi Baru</span>
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[200px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95"
                        >
                            <span class="material-symbols-outlined text-xl">analytics</span>
                            <span>Mulai Evaluasi Juri AI</span>
                        </button>
                    </template>

                    <!-- Failed State -->
                    <template v-if="recordState === 'failed'">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-error text-white font-bold text-xs hover:bg-error/90 transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            @click="uploadRecording"
                        >
                            <span class="material-symbols-outlined text-lg">refresh</span>
                            <span>Coba Unggah Ulang</span>
                        </button>
                    </template>
                </div>

                <!-- Error Alert Box -->
                <div v-if="uploadErrorMessage" class="p-3.5 rounded-2xl bg-error/10 border border-error/30 text-xs text-error max-w-md">
                    {{ uploadErrorMessage }}
                </div>

                <!-- Review Playback Card -->
                <div
                    v-if="audioUrl"
                    class="w-full max-w-md p-4 rounded-2xl bg-surface-subtle border border-border-clean/60 flex flex-col gap-2"
                >
                    <div class="flex items-center justify-between text-xs text-text-muted">
                        <span class="font-bold text-on-surface">Pratinjau Rekaman Audio</span>
                        <span>{{ formattedTime }}</span>
                    </div>
                    <audio
                        :src="audioUrl"
                        controls
                        class="w-full h-10 rounded-xl focus:outline-none"
                    ></audio>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
