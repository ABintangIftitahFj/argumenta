<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

const props = defineProps<{
    status: number;
    message?: string;
}>();

const title = computed(() => {
    return {
        403: 'Akses Ditolak (403)',
        404: 'Halaman Tidak Ditemukan (404)',
        429: 'Batas Kuota Tercapai (429)',
        500: 'Kesalahan Server Internal (500)',
        503: 'Layanan Dalam Perawatan (503)',
    }[props.status] || `Kesalahan (${props.status})`;
});

const description = computed(() => {
    if (props.message) {
        return props.message;
    }

    return {
        403: 'Maaf, Anda tidak memiliki izin untuk mengakses berkas rekaman audio, laporan debat, atau sesi milik debater lain.',
        404: 'Sesi perdebatan atau halaman yang Anda tuju tidak ditemukan atau mungkin telah dipindahkan.',
        429: 'Batas kuota evaluasi AI Anda telah habis untuk saat ini. Silakan upgrade paket atau tunggu periode reset berikutnya.',
        500: 'Terjadi kendala teknis tak terduga pada sistem evaluasi. Sistem kami telah mencatat peristiwa ini untuk ditindaklanjuti.',
        503: 'Sistem sedang dalam proses pemeliharaan berkala. Silakan kembali dalam beberapa saat.',
    }[props.status] || 'Terjadi kesalahan saat memproses permintaan Anda.';
});

const icon = computed(() => {
    return {
        403: 'lock',
        404: 'search_off',
        429: 'hourglass_empty',
        500: 'error',
        503: 'build',
    }[props.status] || 'warning';
});
</script>

<template>
    <Head :title="title" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center min-h-[60vh] max-w-lg mx-auto text-center px-4 py-12">
            <div class="w-20 h-20 rounded-3xl bg-error/15 text-error flex items-center justify-center mb-6 shadow-sm">
                <span class="material-symbols-outlined text-4xl">{{ icon }}</span>
            </div>

            <span class="text-xs font-black uppercase tracking-widest text-error mb-2">
                Status {{ status }}
            </span>

            <h1 class="text-2xl md:text-3xl font-black text-on-surface mb-3 leading-snug">
                {{ title }}
            </h1>

            <p class="text-xs md:text-sm text-text-muted leading-relaxed mb-8 max-w-md">
                {{ description }}
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <Link
                    href="/"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-surface-subtle border border-border-clean text-xs font-bold text-on-surface hover:bg-surface-dim transition-all min-h-[44px]"
                >
                    <span class="material-symbols-outlined text-sm">home</span>
                    <span>Kembali ke Beranda</span>
                </Link>

                <Link
                    href="/practice"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-primary-container text-white text-xs font-bold shadow-sm hover:bg-primary transition-all min-h-[44px]"
                >
                    <span class="material-symbols-outlined text-sm">mic</span>
                    <span>Buka Ruang Latihan</span>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
