<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

interface ModuleItem {
    id: number;
    title: string;
    slug: string;
    stage: string;
    order: number;
    description: string;
    difficulty: string;
    xp_reward: number;
    status: 'locked' | 'available' | 'active' | 'completed';
}

interface UserStats {
    streak: number;
    xp: number;
    weeklyGoal: number;
    completedThisWeek: number;
}

interface RecentSession {
    id: number;
    motion: string;
    format: 'BP' | 'AP';
    role: string;
    score: number;
    date: string;
}

const props = defineProps<{
    appName: string;
    modules?: ModuleItem[];
    stats?: UserStats;
    recentSession?: RecentSession | null;
}>();

const currentStats = computed<UserStats>(() => {
    return props.stats || {
        streak: 5,
        xp: 560,
        weeklyGoal: 5,
        completedThisWeek: 2,
    };
});

const learningModules = computed<ModuleItem[]>(() => {
    return props.modules || [
        {
            id: 1,
            title: 'Dasar Klaim & Premis Argumen',
            slug: 'dasar-klaim-premis',
            stage: 'Tahap 1',
            order: 1,
            description: 'Pelajari struktur AREL dan hindari logical fallacies.',
            difficulty: 'novice',
            xp_reward: 100,
            status: 'completed',
        },
        {
            id: 2,
            title: 'Mekanisme & Rantai Kausalitas',
            slug: 'mekanisme-rantai-kausalitas',
            stage: 'Tahap 2',
            order: 2,
            description: 'Jelaskan mengapa kebijakan berhasil membawa dampak.',
            difficulty: 'novice',
            xp_reward: 120,
            status: 'active',
        },
        {
            id: 3,
            title: 'Rebuttal & Direct Clash',
            slug: 'rebuttal-direct-clash',
            stage: 'Tahap 3',
            order: 3,
            description: 'Menyerang premis utama lawan dan asumsi penting.',
            difficulty: 'intermediate',
            xp_reward: 150,
            status: 'locked',
        },
    ];
});

const activeModule = computed(() => {
    return learningModules.value.find((m) => m.status === 'active') || learningModules.value[0];
});
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

            <!-- 2-Column Responsive Grid: Goal/Stats & Learning Modules -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Daily Goal & Streak Card -->
                <div class="rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col justify-between gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl" aria-hidden="true">target</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-on-surface">Target Mingguan</h2>
                                <p class="text-xs text-text-muted">{{ currentStats.completedThisWeek }} dari {{ currentStats.weeklyGoal }} pidato</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-accent-orange/15 text-on-surface text-xs font-bold">
                            ⚡ {{ currentStats.xp }} XP
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="flex flex-col gap-1.5">
                        <div class="w-full bg-surface-subtle h-3 rounded-full overflow-hidden p-0.5">
                            <div
                                class="bg-primary-container h-full rounded-full transition-all duration-500 ease-out"
                                :style="{ width: `${Math.min(100, Math.round((currentStats.completedThisWeek / Math.max(1, currentStats.weeklyGoal)) * 100))}%` }"
                            ></div>
                        </div>
                        <div class="flex justify-between text-xs text-text-muted font-medium">
                            <span>{{ Math.min(100, Math.round((currentStats.completedThisWeek / Math.max(1, currentStats.weeklyGoal)) * 100)) }}% Selesai</span>
                            <span>{{ Math.max(0, currentStats.weeklyGoal - currentStats.completedThisWeek) }} Sesi Lagi</span>
                        </div>
                    </div>

                    <!-- Streak Row -->
                    <div class="pt-2 border-t border-border-clean/50 flex items-center justify-between text-xs font-medium text-text-muted">
                        <span class="flex items-center gap-1 text-on-surface font-bold">
                            🔥 {{ currentStats.streak }} Hari Berturut-turut
                        </span>
                        <Link href="/progress" class="text-primary font-bold hover:underline min-h-[44px] inline-flex items-center">
                            Detail Progres &rarr;
                        </Link>
                    </div>
                </div>

                <!-- Learning Path Preview Card -->
                <div class="lg:col-span-2 rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col justify-between gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-secondary-container text-secondary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-xl" aria-hidden="true">route</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-on-surface">Jalur Pembelajaran Debat</h2>
                                <p class="text-xs text-text-muted">Modul aktif: {{ activeModule?.title }}</p>
                            </div>
                        </div>

                        <Link
                            href="/learn"
                            class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        >
                            <span>Lihat Semua Kurikulum</span>
                            <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                        </Link>
                    </div>

                    <!-- Modules Horizontal Grid / Carousel -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div
                            v-for="mod in learningModules.slice(0, 3)"
                            :key="mod.id"
                            class="p-4 rounded-2xl border transition-all flex flex-col justify-between gap-2"
                            :class="[
                                mod.status === 'completed' ? 'bg-surface-subtle border-border-clean/50' :
                                mod.status === 'active' ? 'bg-primary-light border-primary-container/40 ring-1 ring-primary-container/30' :
                                'bg-surface-subtle/50 border-border-clean/30 opacity-75'
                            ]"
                        >
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wide"
                                        :class="mod.status === 'completed' ? 'text-text-muted' : mod.status === 'active' ? 'text-primary' : 'text-text-muted'"
                                    >
                                        {{ mod.stage }}
                                    </span>
                                    <span v-if="mod.status === 'completed'" class="material-symbols-outlined text-primary text-base">check_circle</span>
                                    <span v-else-if="mod.status === 'locked'" class="material-symbols-outlined text-text-muted text-base">lock</span>
                                    <span v-else class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
                                </div>
                                <span class="text-sm font-bold text-on-surface line-clamp-1">{{ mod.title }}</span>
                                <span class="text-xs text-text-muted line-clamp-2">{{ mod.description }}</span>
                            </div>

                            <div class="pt-2 border-t border-border-clean/40 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-text-muted">+{{ mod.xp_reward }} XP</span>
                                <Link
                                    v-if="mod.status !== 'locked'"
                                    href="/practice"
                                    class="text-xs font-bold text-primary hover:underline min-h-[36px] inline-flex items-center"
                                >
                                    {{ mod.status === 'completed' ? 'Ulangi' : 'Mulai' }} &rarr;
                                </Link>
                                <span v-else class="text-xs text-text-muted">Terkunci</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-text-muted pt-2 border-t border-border-clean/50">
                        <span>Kurikulum Standar WUDC (World Universities Debating Championship)</span>
                        <Link href="/learn" class="text-primary font-bold hover:underline">Buka Silabus Lengkap &rarr;</Link>
                    </div>
                </div>
            </div>

            <!-- Recent Practice Session Card: Empty State vs Populated State -->
            <section class="rounded-3xl bg-surface-white p-6 shadow-sm border border-border-clean/60 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl" aria-hidden="true">history_edu</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-on-surface">Sesi Latihan Terakhir</h2>
                            <p class="text-xs text-text-muted">Evaluasi pidato dan catatan juri AI terbaru</p>
                        </div>
                    </div>
                </div>

                <!-- Populated State -->
                <div
                    v-if="recentSession"
                    class="p-4 rounded-2xl bg-surface-subtle border border-border-clean/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                >
                    <div class="flex flex-col gap-1 max-w-xl">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full bg-secondary/15 text-secondary text-[10px] font-extrabold uppercase">
                                {{ recentSession.format }}
                            </span>
                            <span class="text-xs font-semibold text-text-muted">• {{ recentSession.role }}</span>
                            <span class="text-xs font-semibold text-text-muted">• {{ recentSession.date }}</span>
                        </div>
                        <h3 class="text-sm font-bold text-on-surface">"{{ recentSession.motion }}"</h3>
                    </div>

                    <div class="flex items-center gap-3 self-end sm:self-center">
                        <div class="flex flex-col items-end">
                            <span class="text-lg font-extrabold text-primary font-serif">{{ recentSession.score }}/100</span>
                            <span class="text-[10px] text-text-muted">Skor Argumen</span>
                        </div>
                        <Link
                            href="/progress"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-primary-container text-white text-xs font-bold shadow-xs hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        >
                            Lihat Feedback
                        </Link>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else
                    class="p-6 rounded-2xl bg-surface-subtle/70 border border-dashed border-border-clean flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left"
                >
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary-light flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-2xl" aria-hidden="true">mic_none</span>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-sm font-bold text-on-surface">Belum Ada Sesi Latihan Pidato</h3>
                            <p class="text-xs text-text-muted max-w-md mt-0.5">
                                Mulai rekaman pidato mandiri pertama Anda untuk mendapatkan analisis argumen AREL, deteksi filler words, dan saran juri AI.
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/practice"
                        class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-2xl bg-primary-container text-white text-xs font-bold shadow-xs hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container shrink-0"
                    >
                        <span class="material-symbols-outlined text-base">mic</span>
                        <span>Mulai Pidato Pertama</span>
                    </Link>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
