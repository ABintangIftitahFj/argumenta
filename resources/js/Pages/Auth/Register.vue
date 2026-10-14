<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    appName: string;
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar Akun — Argumenta" />

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
            <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif mt-2">Mulai Perjalanan Debat Anda</h1>
            <p class="text-xs text-text-muted">Buat akun gratis untuk mengakses latihan format BP/AP dan coaching AI personal.</p>
        </div>

        <!-- Form Card -->
        <div class="w-full max-w-md bg-surface-white rounded-3xl p-6 sm:p-8 border border-border-clean/60 shadow-sm">
            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <!-- Name -->
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-bold text-on-surface uppercase tracking-wider">Nama Lengkap</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Contoh: Alex Pratama"
                        class="w-full px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                        :class="{ 'border-error text-error': form.errors.name }"
                    />
                    <span v-if="form.errors.name" class="text-xs text-error font-medium">
                        {{ form.errors.name }}
                    </span>
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-bold text-on-surface uppercase tracking-wider">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
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
                    <label for="password" class="text-xs font-bold text-on-surface uppercase tracking-wider">Kata Sandi</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                        :class="{ 'border-error text-error': form.errors.password }"
                    />
                    <span v-if="form.errors.password" class="text-xs text-error font-medium">
                        {{ form.errors.password }}
                    </span>
                </div>

                <!-- Password Confirmation -->
                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-on-surface uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                        class="w-full px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                    />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full mt-2 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl bg-primary-container text-white font-bold text-sm shadow-sm hover:bg-primary transition-all min-h-[44px] focus-visible:outline-2 focus-visible:outline-primary-container active:scale-95 disabled:opacity-60"
                >
                    <span v-if="form.processing" class="material-symbols-outlined text-lg animate-spin" aria-hidden="true">progress_activity</span>
                    <span>Buat Akun &amp; Lanjut Onboarding</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-border-clean/60 text-center text-xs text-text-muted">
                Sudah punya akun?
                <Link href="/login" class="font-bold text-primary hover:underline ml-1">
                    Masuk di sini
                </Link>
            </div>
        </div>
    </div>
</template>
