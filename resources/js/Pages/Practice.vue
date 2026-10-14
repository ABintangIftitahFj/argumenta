<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

interface MotionItem {
    id: number;
    title: string;
    info_slide?: string | null;
    theme: string;
    difficulty: string;
    language: string;
    is_featured: boolean;
}

interface RoleMeta {
    name: string;
    team: string;
    side: string;
    order: number;
    brief: string;
    is_reply?: boolean;
}

const props = defineProps<{
    appName: string;
    motions?: MotionItem[];
    bpRoles?: Record<string, RoleMeta>;
    apRoles?: Record<string, RoleMeta>;
    defaults?: {
        format?: 'BP' | 'AP';
        language?: 'id' | 'en';
        difficulty?: 'novice' | 'intermediate' | 'advanced';
    };
}>();

const motionList = computed<MotionItem[]>(() => {
    return props.motions || [
        {
            id: 1,
            title: 'Dewan ini akan mewajibkan transparansi penuh atas penggunaan model AI generatif di sektor publik.',
            theme: 'Teknologi & AI',
            difficulty: 'novice',
            language: 'id',
            is_featured: true,
        },
        {
            id: 2,
            title: 'Dewan ini menyesalkan komersialisasi riset akademik di universitas terkemuka.',
            theme: 'Pendidikan & Sains',
            difficulty: 'intermediate',
            language: 'id',
            is_featured: false,
        },
    ];
});

const defaultBpRoles: Record<string, RoleMeta> = {
    PM: { name: 'Prime Minister', team: 'Opening Government (OG)', side: 'Government', order: 1, brief: 'Tetapkan konteks, mekanisme, dan argumen pembuka.' },
    LO: { name: 'Leader of Opposition', team: 'Opening Opposition (OO)', side: 'Opposition', order: 2, brief: 'Berikan direct clash dan kasus oposisi pembuka.' },
    DPM: { name: 'Deputy Prime Minister', team: 'Opening Government (OG)', side: 'Government', order: 3, brief: 'Rebuild kasus PM dan perdalam dampak kebijakan.' },
    DLO: { name: 'Deputy Leader of Opposition', team: 'Opening Opposition (OO)', side: 'Opposition', order: 4, brief: 'Perkuat serangan LO dan kunci posisi meja pembuka.' },
    MG: { name: 'Member of Government', team: 'Closing Government (CG)', side: 'Government', order: 5, brief: 'Bawakan ekstensi argumen baru yang unik dari OG.' },
    MO: { name: 'Member of Opposition', team: 'Closing Opposition (CO)', side: 'Opposition', order: 6, brief: 'Bawakan ekstensi oposisi baru yang berbeda dari OO.' },
    GW: { name: 'Government Whip', team: 'Closing Government (CG)', side: 'Government', order: 7, brief: 'Timbang dampak dan simpulkan kemenangan CG. Tanpa argumen baru.' },
    OW: { name: 'Opposition Whip', team: 'Closing Opposition (CO)', side: 'Opposition', order: 8, brief: 'Pidato penutup debat: pertahankan MO dan simpulkan keunggulan CO.' },
};

const defaultApRoles: Record<string, RoleMeta> = {
    PM: { name: 'Prime Minister', team: 'Government', side: 'Government', order: 1, brief: 'Definisikan mosi dan kasus awal pemerintah.' },
    LO: { name: 'Leader of Opposition', team: 'Opposition', side: 'Opposition', order: 2, brief: 'Klarifikasi posisi oposisi dan sanggah PM.' },
    DPM: { name: 'Deputy Prime Minister', team: 'Government', side: 'Government', order: 3, brief: 'Pertahankan PM dan selesaikan kasus pemerintah.' },
    DLO: { name: 'Deputy Leader of Opposition', team: 'Opposition', side: 'Opposition', order: 4, brief: 'Bantah DPM dan selesaikan kasus oposisi.' },
    GW: { name: 'Government Whip', team: 'Government', side: 'Government', order: 5, brief: 'Ringkas pertarungan utama dan sanggah seluruh oposisi.' },
    OW: { name: 'Opposition Whip', team: 'Opposition', side: 'Opposition', order: 6, brief: 'Ringkas pertarungan dan sanggah seluruh kasus pemerintah.' },
    OR: { name: 'Opposition Reply', team: 'Opposition', side: 'Opposition', order: 7, is_reply: true, brief: 'Pidato evaluasi 4 menit membuktikan kemenangan oposisi.' },
    GR: { name: 'Government Reply', team: 'Government', side: 'Government', order: 8, is_reply: true, brief: 'Pidato penutup 4 menit membuktikan kemenangan pemerintah.' },
};

const bpRolesMap = computed(() => props.bpRoles || defaultBpRoles);
const apRolesMap = computed(() => props.apRoles || defaultApRoles);

const activeRoles = computed(() => {
    return form.format === 'BP' ? bpRolesMap.value : apRolesMap.value;
});

const isCustomMotion = ref(false);

const form = useForm({
    format: (props.defaults?.format || 'BP') as 'BP' | 'AP',
    role: 'PM',
    language: (props.defaults?.language || 'id') as 'id' | 'en',
    difficulty: (props.defaults?.difficulty || 'novice') as 'novice' | 'intermediate' | 'advanced',
    motion_id: motionList.value[0]?.id || null,
    custom_motion: '',
    ai_coaching_enabled: true,
});

// Reset role when format changes if role is invalid for new format
watch(() => form.format, (newFormat) => {
    const roles = newFormat === 'BP' ? bpRolesMap.value : apRolesMap.value;
    if (!roles[form.role]) {
        form.role = 'PM';
    }
});

const selectedRoleMeta = computed<RoleMeta | undefined>(() => {
    return activeRoles.value[form.role];
});

const selectedMotion = computed(() => {
    if (isCustomMotion.value) {
        return form.custom_motion ? form.custom_motion : 'Mosi kustom belum diisi';
    }
    const found = motionList.value.find((m) => m.id === form.motion_id);
    return found ? found.title : motionList.value[0]?.title || '';
});

const speechDurationText = computed(() => {
    return selectedRoleMeta.value?.is_reply ? '4 Menit (Reply Speech)' : '7 Menit (Standar WUDC)';
});

const poiRuleText = computed(() => {
    return selectedRoleMeta.value?.is_reply
        ? 'Dilarang (Protected Full Speech)'
        : 'Aktif antara Menit 1:00 s.d. 6:00 (Bel 1 kali)';
});

const submit = () => {
    if (isCustomMotion.value) {
        form.motion_id = null;
    }
    form.post('/practice');
};
</script>

<template>
    <Head title="Setup Latihan Pidato — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-4xl mx-auto">
            <header class="flex flex-col gap-1">
                <span class="text-xs font-bold uppercase tracking-wider text-primary">Setup Latihan Pidato</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface font-serif">Konfigurasi Sesi Debat</h1>
                <p class="text-sm text-text-muted">Pilih sistem kompetisi, mosi, posisi peran, dan aturan pidato Anda.</p>
            </header>

            <form class="rounded-3xl bg-surface-white p-6 sm:p-8 shadow-sm border border-border-clean/60 flex flex-col gap-6" @submit.prevent="submit">
                <!-- 1. Format Debat -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-text-muted">1. Format Kompetisi Debat</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" role="radiogroup">
                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.format === 'BP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.format = 'BP'"
                        >
                            <div class="flex items-center justify-between w-full">
                                <span class="text-sm font-bold text-on-surface">British Parliamentary (BP)</span>
                                <span class="px-2 py-0.5 rounded-full bg-secondary/15 text-secondary text-[10px] font-extrabold uppercase">
                                    WUDC / NUDC
                                </span>
                            </div>
                            <span class="text-xs text-text-muted">4 Tim (OG, OO, CG, CO) • 8 Pembicara • 7 Menit per pidato</span>
                        </button>

                        <button
                            type="button"
                            class="flex flex-col items-start gap-1 p-4 rounded-2xl border transition-all text-left min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.format === 'AP' ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                            @click="form.format = 'AP'"
                        >
                            <div class="flex items-center justify-between w-full">
                                <span class="text-sm font-bold text-on-surface">Asian Parliamentary (AP)</span>
                                <span class="px-2 py-0.5 rounded-full bg-primary-container text-white text-[10px] font-extrabold uppercase">
                                    UADC / KDMI
                                </span>
                            </div>
                            <span class="text-xs text-text-muted">2 Tim (Gov vs Opp) • 3 Pembicara + 1 Reply (4 menit)</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Mosi Debat -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-muted">2. Mosi Perdebatan</label>
                        <button
                            type="button"
                            class="text-xs font-bold text-primary hover:underline min-h-[36px] inline-flex items-center"
                            @click="isCustomMotion = !isCustomMotion"
                        >
                            {{ isCustomMotion ? 'Pilih dari Katalog Mosi' : '+ Tulis Mosi Kustom Sendiri' }}
                        </button>
                    </div>

                    <!-- Custom Motion Input -->
                    <div v-if="isCustomMotion" class="flex flex-col gap-1.5">
                        <textarea
                            v-model="form.custom_motion"
                            rows="3"
                            placeholder="Ketik mosi debat Anda di sini (misal: 'Dewan ini akan mewajibkan...')"
                            class="w-full p-4 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container resize-none min-h-[44px]"
                            :class="{ 'border-error text-error': form.errors.custom_motion }"
                        ></textarea>
                        <span v-if="form.errors.custom_motion" class="text-xs text-error font-medium">
                            {{ form.errors.custom_motion }}
                        </span>
                    </div>

                    <!-- Curated Motion Picker -->
                    <div v-else class="flex flex-col gap-2">
                        <div class="grid grid-cols-1 gap-2.5 max-h-64 overflow-y-auto pr-1">
                            <button
                                v-for="item in motionList"
                                :key="item.id"
                                type="button"
                                class="p-3.5 rounded-2xl border text-left transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container flex flex-col gap-1"
                                :class="form.motion_id === item.id ? 'border-primary-container bg-primary-light ring-2 ring-primary-container/30' : 'border-border-clean hover:bg-surface-subtle'"
                                @click="form.motion_id = item.id"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full bg-surface-subtle border border-border-clean text-[10px] font-bold text-on-surface">
                                        {{ item.theme }}
                                    </span>
                                    <span v-if="item.is_featured" class="px-2 py-0.5 rounded-full bg-primary-container text-white text-[10px] font-extrabold">
                                        Rekomendasi Hari Ini
                                    </span>
                                </div>
                                <span class="text-sm font-semibold text-on-surface leading-snug">"{{ item.title }}"</span>
                                <span v-if="item.info_slide" class="text-xs text-text-muted italic line-clamp-1">Info Slide: {{ item.info_slide }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Posisi Pembicara -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-muted">
                            3. Posisi Peran Pembicara (Format: {{ form.format }})
                        </label>
                        <span v-if="selectedRoleMeta" class="text-xs font-bold text-primary">
                            {{ selectedRoleMeta.team }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button
                            v-for="(meta, code) in activeRoles"
                            :key="code"
                            type="button"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border transition-all min-h-[44px] text-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            :class="form.role === code ? 'border-primary-container bg-primary-light text-primary ring-2 ring-primary-container/30 font-bold' : 'border-border-clean hover:bg-surface-subtle text-on-surface'"
                            @click="form.role = code as string"
                        >
                            <span class="text-xs font-extrabold">{{ code }}</span>
                            <span class="text-[10px] text-text-muted truncate max-w-full">{{ meta.name }}</span>
                        </button>
                    </div>

                    <span v-if="form.errors.role" class="text-xs text-error font-medium">
                        {{ form.errors.role }}
                    </span>
                </div>

                <!-- 4. Bahasa & Tingkat Kesulitan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Bahasa -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-text-muted">4. Bahasa Pidato</label>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="flex-1 py-2.5 rounded-2xl border text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                                :class="form.language === 'id' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                                @click="form.language = 'id'"
                            >
                                Bahasa Indonesia
                            </button>
                            <button
                                type="button"
                                class="flex-1 py-2.5 rounded-2xl border text-xs font-bold transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container"
                                :class="form.language === 'en' ? 'bg-primary-container text-white border-primary-container' : 'bg-surface-subtle text-on-surface border-border-clean hover:bg-surface-dim'"
                                @click="form.language = 'en'"
                            >
                                English
                            </button>
                        </div>
                    </div>

                    <!-- Kesulitan -->
                    <div class="flex flex-col gap-2">
                        <label for="difficulty-select" class="text-xs font-bold uppercase tracking-wider text-text-muted">Tingkat Penjurian AI</label>
                        <select
                            id="difficulty-select"
                            v-model="form.difficulty"
                            class="px-4 py-2.5 rounded-2xl bg-surface-subtle border border-border-clean text-xs font-bold text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                        >
                            <option value="novice">Novice (Pemula — Fokus Struktur AREL)</option>
                            <option value="intermediate">Intermediate (Menengah — Fokus Rebuttal &amp; Kausalitas)</option>
                            <option value="advanced">Advanced (Mahir — Standar WUDC &amp; Comparative Weighing)</option>
                        </select>
                    </div>
                </div>

                <!-- 5. Summary Card & Rules Preview -->
                <div class="p-4 rounded-2xl bg-surface-subtle border border-border-clean flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-on-surface">Kompilasi Aturan Sesi:</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-primary-light text-primary text-[10px] font-extrabold uppercase">
                            {{ form.format }} • {{ form.role }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center gap-2 text-text-muted">
                            <span class="material-symbols-outlined text-base text-primary">timer</span>
                            <span>Durasi Pidato: <strong class="text-on-surface">{{ speechDurationText }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2 text-text-muted">
                            <span class="material-symbols-outlined text-base text-primary">notifications_active</span>
                            <span>Aturan POI: <strong class="text-on-surface">{{ poiRuleText }}</strong></span>
                        </div>
                    </div>

                    <div v-if="selectedRoleMeta" class="text-xs text-text-muted pt-2 border-t border-border-clean/50 leading-relaxed">
                        <span class="font-bold text-on-surface">Tugas Peran ({{ selectedRoleMeta.name }}):</span>
                        {{ selectedRoleMeta.brief }}
                    </div>
                </div>

                <!-- Submit CTA -->
                <div class="pt-4 border-t border-border-clean/60 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-text-muted min-h-[44px]">
                        <input
                            v-model="form.ai_coaching_enabled"
                            type="checkbox"
                            class="w-4 h-4 rounded text-primary-container focus:ring-primary-container cursor-pointer"
                        />
                        <span>Aktifkan asisten persiapan Coach Kiko (Coached Practice)</span>
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95 disabled:opacity-60"
                    >
                        <span v-if="form.processing" class="material-symbols-outlined text-lg animate-spin" aria-hidden="true">progress_activity</span>
                        <span>Lanjut ke Persiapan Kasus</span>
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
