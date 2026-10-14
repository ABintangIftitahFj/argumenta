<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    appName: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk — Argumenta" />

    <div class="min-h-screen bg-background-cream text-on-surface flex flex-col justify-center items-center px-4 py-8">
        <!-- Logo & Header -->
        <div class="flex flex-col items-center gap-2 mb-8 text-center max-w-sm">
            <Link href="/" class="flex items-center gap-3 p-2 rounded-2xl focus-visible:outline-2 focus-visible:outline-primary-container">
                <div class="w-12 h-12 rounded-2xl bg-primary-container flex items-center justify-center text-white shadow-sm">
                    <span class="material-symbols-outlined text-2xl" aria-hidden="true">forum</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-extrabold text-2xl tracking-tight text-on-surface leading-tight font-serif">
                        {{ appName }}
                    </span>
                    <span class="text-[11px] uppercase font-bold tracking-wider text-secondary">
                        AI Debate Coach
                    </span>
                </div>
            </Link>
            <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif mt-2">Selamat Datang Kembali</h1>
            <p class="text-xs text-text-muted">Masuk untuk melanjutkan sesi latihan, streak harian, dan evaluasi juri AI.</p>
        </div>

        <!-- Form Card -->
        <div class="w-full max-w-md bg-surface-white rounded-3xl p-6 sm:p-8 border border-border-clean/60 shadow-sm">
            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-bold text-on-surface uppercase tracking-wider">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@email.com"
                        class="w-full px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                        :class="{ 'border-error text-error': form.errors.email }"
                    />
                    <span v-if="form.errors.email" class="text-xs text-error font-medium">
                        {{ form.errors.email }}
                    </span>
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="text-xs font-bold text-on-surface uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                        :class="{ 'border-error text-error': form.errors.password }"
                    />
                    <span v-if="form.errors.password" class="text-xs text-error font-medium">
                        {{ form.errors.password }}
                    </span>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-text-muted min-h-[44px]">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 rounded text-primary-container focus:ring-primary-container cursor-pointer"
                        />
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95 disabled:opacity-60"
                >
                    <span v-if="form.processing" class="material-symbols-outlined text-lg animate-spin" aria-hidden="true">progress_activity</span>
                    <span>Masuk ke Akun</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-border-clean/60 text-center text-xs text-text-muted">
                Belum punya akun?
                <Link href="/register" class="font-bold text-primary hover:underline ml-1">
                    Daftar sekarang
                </Link>
            </div>
        </div>
    </div>
</template>
