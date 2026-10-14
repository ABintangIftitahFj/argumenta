<script setup lang="ts">
defineProps<{
    appName: string;
}>();
</script>

<template>
    <main class="min-h-screen px-6 py-10 sm:px-10">
        <section class="mx-auto max-w-3xl">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">
                AI Debate Coach
            </p>
            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                Latih argumen. Bicara lebih kuat.
            </h1>
            <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                {{ appName }} sedang disiapkan untuk latihan debat yang terarah dan suportif.
            </p>
        </section>
    </main>
</template>
