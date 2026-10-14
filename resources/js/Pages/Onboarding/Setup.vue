<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    appName: string;
    initialPreferences?: {
        language?: string;
        learning_goal?: string;
        skill_level?: string;
        preferred_format?: string;
        weekly_goal?: number;
    };
}>();

const currentStep = ref(1);
const totalSteps = 4;

const form = useForm({
    language: props.initialPreferences?.language || 'id',
    preferred_format: props.initialPreferences?.preferred_format || 'BP',
    skill_level: props.initialPreferences?.skill_level || 'novice',
    learning_goal: props.initialPreferences?.learning_goal || 'competition',
    weekly_goal: props.initialPreferences?.weekly_goal || 5,
});

const progressPercentage = computed(() => {
    return Math.round((currentStep.value / totalSteps) * 100);
});

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    } else {
        submit();
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submit = () => {
    form.post('/onboarding');
};
</script>

<template>
    <Head title="Setup Onboarding — Argumenta" />

    <div class="min-h-screen bg-background-cream text-on-surface flex flex-col justify-between items-center px-4 py-8">
        <!-- Top Header & Progress -->
        <header class="w-full max-w-xl flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-primary-container flex items-center justify-center text-white shadow-xs">
                        <span class="material-symbols-outlined text-xl" aria-hidden="true">forum</span>
                    </div>
                    <span class="font-extrabold text-lg text-on-surface font-serif">{{ appName }}</span>
                </div>
                <span class="text-xs font-bold text-text-muted">Langkah {{ currentStep }} dari {{ totalSteps }}</span>
            </div>

            <!-- Progress Bar -->
            <div class="w-full h-2 rounded-full bg-surface-subtle overflow-hidden">
                <div
                    class="h-full bg-primary-container transition-all duration-300 rounded-full"
                    :style="{ width: `${progressPercentage}%` }"
                ></div>
            </div>
        </header>

        <!-- Step Content Card -->
        <main class="w-full max-w-xl my-auto py-6">
            <div class="bg-surface-white rounded-3xl p-6 sm:p-8 border border-border-clean/60 shadow-sm flex flex-col gap-6">
                <!-- Mascot Message Bubble -->
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-primary-light border border-primary-container/20">
                    <div class="w-10 h-10 rounded-xl bg-surface-white flex items-center justify-center shrink-0 border border-primary-container/20 text-primary font-bold">
                        <span class="material-symbols-outlined text-2xl">smart_toy</span>
                    </div>
                    <p class="text-xs sm:text-sm text-on-surface font-medium leading-relaxed">
                        <span class="font-bold text-primary">Kiko:</span>
                        <template v-if="currentStep === 1">
                            "Pilih bahasa utama Anda. Kita bisa berlatih dalam Bahasa Indonesia maupun English!"
                        </template>
                        <template v-else-if="currentStep === 2">
                            "Format apa yang ingin Anda kuasai terlebih dahulu? British Parliamentary atau Asian Parliamentary?"
                        </template>
                        <template v-else-if="currentStep === 3">
                            "Berapa banyak pengalaman debat yang Anda miliki saat ini? Jangan khawatir, pemula sangat disambut!"
                        </template>
                        <template v-else>
                            "Apa tujuan utama Anda berlatih debat dan berapa target pidato Anda setiap minggu?"
                        </template>
                    </p>
                </div>

                <!-- STEP 1: Bahasa -->
                <section v-if="currentStep === 1" class="flex flex-col gap-4" aria-labelledby="step1-title">
                    <div>
                        <h1 id="step1-title" class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif">
                            Pilih Bahasa Latihan
                        </h1>
                        <p class="text-xs sm:text-sm text-text-muted mt-1">
                            Bahasa ini akan digunakan untuk instruksi latihan, transkripsi pidato, dan evaluasi juri AI.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup">
                        <button
                            type="button"
                            class="flex items-center gap-3 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.language === 'id' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.language = 'id'"
                        >
                            <span class="text-2xl" aria-hidden="true">🇮🇩</span>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Bahasa Indonesia</span>
                                <span class="text-xs text-text-muted">Standar KDMI &amp; turnamen nasional</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="flex items-center gap-3 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.language === 'en' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.language = 'en'"
                        >
                            <span class="text-2xl" aria-hidden="true">🇬🇧</span>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">English</span>
                                <span class="text-xs text-text-muted">Standard WUDC &amp; international circuit</span>
                            </div>
                        </button>
                    </div>
                </section>

                <!-- STEP 2: Format Debat -->
                <section v-else-if="currentStep === 2" class="flex flex-col gap-4" aria-labelledby="step2-title">
                    <div>
                        <h1 id="step2-title" class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif">
                            Format Debat Favorit
                        </h1>
                        <p class="text-xs sm:text-sm text-text-muted mt-1">
                            Pilih sistem kompetisi debat yang ingin Anda fokuskan terlebih dahulu. Anda bisa beralih kapan saja.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3" role="radiogroup">
                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.preferred_format === 'BP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.preferred_format = 'BP'"
                        >
                            <div class="flex items-center justify-between w-full">
                                <span class="text-sm font-bold text-on-surface">British Parliamentary (BP)</span>
                                <span class="px-2 py-0.5 rounded-full bg-secondary/15 text-secondary text-[10px] font-extrabold uppercase">
                                    WUDC / NUDC
                                </span>
                            </div>
                            <span class="text-xs text-text-muted mt-1">
                                4 Tim per ruang (OG, OO, CG, CO) • 8 Pembicara • 7 Menit per pidato + POI
                            </span>
                        </button>

                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.preferred_format === 'AP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.preferred_format = 'AP'"
                        >
                            <div class="flex items-center justify-between w-full">
                                <span class="text-sm font-bold text-on-surface">Asian Parliamentary (AP)</span>
                                <span class="px-2 py-0.5 rounded-full bg-primary-container text-white text-[10px] font-extrabold uppercase">
                                    UADC / KDMI
                                </span>
                            </div>
                            <span class="text-xs text-text-muted mt-1">
                                2 Tim (Government vs Opposition) • 3 Pembicara + 1 Reply • 7 Menit per pidato
                            </span>
                        </button>
                    </div>
                </section>

                <!-- STEP 3: Tingkat Kemampuan -->
                <section v-else-if="currentStep === 3" class="flex flex-col gap-4" aria-labelledby="step3-title">
                    <div>
                        <h1 id="step3-title" class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif">
                            Tingkat Pengalaman Anda
                        </h1>
                        <p class="text-xs sm:text-sm text-text-muted mt-1">
                            Ini membantu Kiko menyesuaikan kedalaman feedback dan rekomendasi kurikulum.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3" role="radiogroup">
                        <button
                            type="button"
                            class="flex items-center gap-4 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.skill_level === 'novice' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.skill_level = 'novice'"
                        >
                            <span class="text-2xl" aria-hidden="true">🌱</span>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Novice (Pemula)</span>
                                <span class="text-xs text-text-muted">Baru mengenal debat kompetitif, ingin memahami struktur AREL dasar</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="flex items-center gap-4 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.skill_level === 'intermediate' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.skill_level = 'intermediate'"
                        >
                            <span class="text-2xl" aria-hidden="true">🌿</span>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Intermediate (Menengah)</span>
                                <span class="text-xs text-text-muted">Pernah ikut beberapa lomba, ingin memperdalam rebuttal &amp; weighing</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            class="flex items-center gap-4 p-4 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.skill_level === 'advanced' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.skill_level = 'advanced'"
                        >
                            <span class="text-2xl" aria-hidden="true">🌳</span>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-on-surface">Advanced (Berpengalaman)</span>
                                <span class="text-xs text-text-muted">Debater aktif turnamen nasional/internasional, fokus pada extension &amp; whip mastery</span>
                            </div>
                        </button>
                    </div>
                </section>

                <!-- STEP 4: Tujuan & Target Mingguan -->
                <section v-else class="flex flex-col gap-4" aria-labelledby="step4-title">
                    <div>
                        <h1 id="step4-title" class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif">
                            Tujuan &amp; Target Mingguan
                        </h1>
                        <p class="text-xs sm:text-sm text-text-muted mt-1">
                            Pilih alasan Anda berlatih dan komitmen pidato mingguan Anda.
                        </p>
                    </div>

                    <div class="flex flex-col gap-3">
                        <label class="text-xs font-bold text-text-muted uppercase tracking-wider">Tujuan Belajar</label>
                        <div class="grid grid-cols-2 gap-2" role="radiogroup">
                            <button
                                v-for="(goal, key) in {
                                    competition: 'Kompetisi Resmi',
                                    academic: 'Kuliah & Sekolah',
                                    career: 'Karier & Public Speaking',
                                    casual: 'Hobi & Logika'
                                }"
                                :key="key"
                                type="button"
                                class="p-3 rounded-2xl border text-xs font-bold text-center transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                                :class="form.learning_goal === key ? 'border-primary-container bg-primary-light text-primary ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle text-on-surface'"
                                @click="form.learning_goal = key"
                            >
                                {{ goal }}
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <label for="weekly-goal-input" class="text-xs font-bold text-text-muted uppercase tracking-wider">
                            Target Pidato / Minggu: <span class="text-primary font-extrabold">{{ form.weekly_goal }} Sesi</span>
                        </label>
                        <input
                            id="weekly-goal-input"
                            v-model.number="form.weekly_goal"
                            type="range"
                            min="1"
                            max="14"
                            step="1"
                            class="w-full accent-primary-container h-2 bg-surface-subtle rounded-lg cursor-pointer min-h-[44px]"
                        />
                        <div class="flex justify-between text-[11px] text-text-muted">
                            <span>1 (Santai)</span>
                            <span>5 (Rekomendasi)</span>
                            <span>14 (Intensif)</span>
                        </div>
                    </div>
                </section>

                <!-- Navigation Controls -->
                <div class="pt-4 border-t border-border-clean/60 flex items-center justify-between gap-3">
                    <button
                        v-if="currentStep > 1"
                        type="button"
                        class="px-5 py-2.5 rounded-2xl bg-surface-subtle border border-border-clean text-xs font-bold hover:bg-surface-dim transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        @click="prevStep"
                    >
                        Kembali
                    </button>
                    <span v-else></span>

                    <button
                        type="button"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95 disabled:opacity-60"
                        @click="nextStep"
                    >
                        <span v-if="form.processing" class="material-symbols-outlined text-lg animate-spin" aria-hidden="true">progress_activity</span>
                        <span>{{ currentStep === totalSteps ? 'Selesai & Mulai Latihan' : 'Lanjutkan' }}</span>
                        <span v-if="currentStep < totalSteps" class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </button>
                </div>
            </div>
        </main>

        <footer class="text-xs text-text-muted text-center">
            PWA AI Debate Coach • Anda dapat mengubah preferensi kapan saja di menu Profil
        </footer>
    </div>
</template>
