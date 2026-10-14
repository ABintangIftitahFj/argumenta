<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const appName = computed(() => (page.props as any).appName || 'Argumenta');
const auth = computed(() => (page.props as any).auth);
const user = computed(() => auth.value?.user);

const currentPath = computed(() => {
    return page.url.split('?')[0];
});

interface NavItem {
    name: string;
    href: string;
    icon: string;
    mobilePrimary?: boolean;
}

const navItems: NavItem[] = [
    { name: 'Beranda', href: '/', icon: 'home', mobilePrimary: true },
    { name: 'Belajar', href: '/learn', icon: 'school', mobilePrimary: true },
    { name: 'Latihan', href: '/practice', icon: 'mic', mobilePrimary: true },
    { name: 'Coach Kiko', href: '/coach', icon: 'smart_toy', mobilePrimary: false },
    { name: 'Progres', href: '/progress', icon: 'insights', mobilePrimary: true },
    { name: 'Profil', href: '/profile', icon: 'account_circle', mobilePrimary: true },
];

const mobileNavItems = computed(() => {
    return navItems.filter((item) => item.mobilePrimary !== false);
});

const isCurrent = (href: string) => {
    if (href === '/') {
        return currentPath.value === '/' || currentPath.value === '';
    }
    return currentPath.value.startsWith(href);
};

const streakDays = computed(() => user.value?.profile?.streak_count ?? 5);
const currentXp = computed(() => user.value?.profile?.total_xp ?? 560);
const activeLanguage = ref<'id' | 'en'>(user.value?.profile?.language || 'id');

const toggleLanguage = (lang: 'id' | 'en') => {
    activeLanguage.value = lang;
};
</script>

<template>
    <div class="min-h-screen bg-background-cream text-on-surface flex flex-col font-sans">
        <!-- Accessibility Skip Link -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 z-[100] px-4 py-2.5 rounded-xl bg-primary text-white font-bold text-sm shadow-md ring-2 ring-primary-container transition-all"
        >
            Lewati ke konten utama
        </a>

        <!-- Desktop Sidebar (>= 1024px) -->
        <aside
            class="hidden lg:flex fixed left-0 top-0 h-full w-64 bg-surface-white z-50 flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)] border-r border-border-clean/60"
            aria-label="Navigasi Utama Desktop"
        >
            <div class="flex flex-col">
                <!-- Brand Header -->
                <div class="p-5 flex flex-col gap-2">
                    <Link
                        href="/"
                        class="flex items-center gap-3 rounded-xl p-1 -m-1 focus-visible:outline-2 focus-visible:outline-primary-container min-h-[44px]"
                    >
                        <div class="w-10 h-10 rounded-2xl bg-primary-container flex items-center justify-center text-white shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-2xl" aria-hidden="true">forum</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-xl tracking-tight text-on-surface leading-tight font-serif">
                                {{ appName }}
                            </span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-secondary mt-0.5">
                                AI Debate Coach
                            </span>
                        </div>
                    </Link>

                    <div class="mt-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-light text-primary text-xs font-bold tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse" aria-hidden="true"></span>
                            BP &amp; AP Debate
                        </span>
                    </div>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="flex flex-col gap-1 px-3 mt-2" aria-label="Menu navigasi">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        :aria-current="isCurrent(item.href) ? 'page' : undefined"
                        class="flex items-center gap-3 px-3 py-3 rounded-2xl min-h-[44px] transition-all group focus-visible:outline-2 focus-visible:outline-primary-container"
                        :class="[
                            isCurrent(item.href)
                                ? 'bg-primary-light text-primary font-bold shadow-xs'
                                : 'text-on-surface-variant font-medium hover:bg-surface-subtle hover:text-on-surface'
                        ]"
                    >
                        <span
                            class="material-symbols-outlined text-2xl transition-colors shrink-0"
                            :class="isCurrent(item.href) ? 'text-primary' : 'text-text-muted group-hover:text-primary'"
                            aria-hidden="true"
                        >
                            {{ item.icon }}
                        </span>
                        <span class="text-sm font-semibold tracking-tight">{{ item.name }}</span>
                    </Link>
                </nav>
            </div>

            <!-- Sidebar User Card & Footer -->
            <div class="p-3 flex flex-col gap-2 border-t border-border-clean/50">
                <!-- Authenticated User -->
                <div v-if="user" class="p-3 rounded-2xl bg-surface-subtle flex flex-col gap-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="relative w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-lg" aria-hidden="true">person</span>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-primary-container ring-2 ring-surface-white" title="Aktif"></span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-on-surface truncate">{{ user.name }}</span>
                                <span class="text-[11px] text-text-muted capitalize truncate">{{ user.profile?.skill_level || 'Novice' }}</span>
                            </div>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="p-1.5 rounded-xl text-text-muted hover:text-error hover:bg-surface-white transition-all min-h-[36px] min-w-[36px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                            title="Keluar"
                        >
                            <span class="material-symbols-outlined text-base">logout</span>
                        </Link>
                    </div>
                </div>

                <!-- Guest State -->
                <div v-else class="p-3 rounded-2xl bg-surface-subtle flex items-center justify-between gap-2">
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-on-surface">Mode Tamu</span>
                        <span class="text-[11px] text-text-muted">Simpan progresmu</span>
                    </div>
                    <Link
                        href="/login"
                        class="px-3 py-1.5 rounded-xl bg-primary-container text-white text-xs font-bold hover:bg-primary transition-all min-h-[36px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-primary-container"
                    >
                        Masuk
                    </Link>
                </div>

                <div class="flex items-center justify-between px-2 py-1 text-xs text-text-muted">
                    <span class="font-medium">Argumenta v1.0</span>
                    <span class="flex items-center gap-1 text-primary font-bold">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        PWA
                    </span>
                </div>
            </div>
        </aside>

        <!-- Top Header for Desktop & Mobile -->
        <header
            class="fixed top-0 inset-x-0 lg:left-64 z-40 h-16 bg-surface-white/90 backdrop-blur-md border-b border-border-clean/50 flex items-center justify-between px-4 lg:px-8 shadow-xs"
        >
            <!-- Mobile Brand Title / Desktop Breadcrumb -->
            <div class="flex items-center gap-2">
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-primary-container flex items-center justify-center text-white shrink-0 shadow-xs">
                        <span class="material-symbols-outlined text-xl" aria-hidden="true">forum</span>
                    </div>
                    <span class="font-extrabold text-lg text-on-surface font-serif tracking-tight">
                        {{ appName }}
                    </span>
                </div>
                <div class="hidden lg:flex items-center gap-2 text-xs font-semibold text-text-muted">
                    <span class="material-symbols-outlined text-base text-primary">school</span>
                    <span>Argumenta</span>
                    <span class="text-border-clean">/</span>
                    <span class="text-on-surface font-bold">Arena Debat</span>
                </div>
            </div>

            <!-- Top Right Indicators (Streak, XP, Language, Profile) -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Streak Pill -->
                <div
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-full bg-accent-orange/15 text-on-surface text-xs font-bold min-h-[36px]"
                    title="Streak Latihan Aktif"
                >
                    <span class="text-sm" aria-hidden="true">🔥</span>
                    <span>{{ streakDays }} Hari</span>
                </div>

                <!-- XP Pill -->
                <div
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-full bg-primary-light text-primary text-xs font-bold min-h-[36px]"
                    title="Total XP Anda"
                >
                    <span class="text-sm" aria-hidden="true">⚡</span>
                    <span>{{ currentXp }} XP</span>
                </div>

                <!-- Language Switcher Pill -->
                <div
                    class="flex items-center bg-surface-subtle p-0.5 rounded-full text-xs font-bold min-h-[36px]"
                    role="group"
                    aria-label="Pilihan Bahasa"
                >
                    <button
                        type="button"
                        class="px-2 py-1 rounded-full transition-all min-h-[32px] min-w-[32px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        :class="activeLanguage === 'id' ? 'bg-surface-white text-on-surface shadow-xs font-extrabold' : 'text-text-muted hover:text-on-surface'"
                        @click="toggleLanguage('id')"
                    >
                        ID
                    </button>
                    <button
                        type="button"
                        class="px-2 py-1 rounded-full transition-all min-h-[32px] min-w-[32px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        :class="activeLanguage === 'en' ? 'bg-surface-white text-on-surface shadow-xs font-extrabold' : 'text-text-muted hover:text-on-surface'"
                        @click="toggleLanguage('en')"
                    >
                        EN
                    </button>
                </div>

                <!-- Guest Login / Authenticated Profile Button -->
                <template v-if="!user">
                    <Link
                        href="/login"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-primary-light text-primary text-xs font-bold hover:bg-primary hover:text-white transition-all min-h-[36px] focus-visible:outline-2 focus-visible:outline-primary-container"
                    >
                        <span class="material-symbols-outlined text-sm">login</span>
                        <span>Masuk</span>
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/profile"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-subtle border border-border-clean text-on-surface text-xs font-bold hover:bg-surface-dim transition-all min-h-[36px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        :title="`Profil: ${user.name}`"
                    >
                        <span class="material-symbols-outlined text-sm text-primary">person</span>
                        <span class="truncate max-w-[100px]">{{ user.name }}</span>
                    </Link>
                </template>

                <!-- AI Coach Quick Action Button (Desktop Only) -->
                <Link
                    href="/coach"
                    class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-container text-white text-xs font-bold shadow-xs hover:bg-primary transition-all min-h-[36px] focus-visible:outline-2 focus-visible:outline-primary-container"
                >
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">smart_toy</span>
                    <span>Tanya Kiko</span>
                </Link>
            </div>
        </header>

        <!-- Main Content Area -->
        <main
            id="main-content"
            class="flex-1 lg:pl-64 pt-16 pb-24 lg:pb-10 transition-all focus:outline-none"
            tabindex="-1"
        >
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <slot />
            </div>
        </main>

        <!-- Mobile Bottom Navigation Bar (< 1024px) -->
        <nav
            class="lg:hidden fixed bottom-0 inset-x-0 z-50 pb-safe bg-surface-white/95 backdrop-blur-xl border-t border-border-clean/60 shadow-[0_-4px_16px_rgba(23,78,67,0.06)]"
            aria-label="Navigasi Bawah Seluler"
        >
            <div class="flex justify-around items-center h-18 px-2 max-w-lg mx-auto">
                <Link
                    v-for="item in mobileNavItems"
                    :key="item.href"
                    :href="item.href"
                    :aria-current="isCurrent(item.href) ? 'page' : undefined"
                    class="flex flex-col items-center justify-center gap-1 w-16 h-14 rounded-2xl min-h-[44px] min-w-[44px] transition-all duration-200 focus-visible:outline-2 focus-visible:outline-primary-container select-none active:scale-95"
                    :class="[
                        isCurrent(item.href)
                            ? 'bg-primary-light text-primary font-bold shadow-xs'
                            : 'text-text-muted hover:text-on-surface'
                    ]"
                >
                    <span
                        class="material-symbols-outlined text-[24px]"
                        :class="isCurrent(item.href) ? 'text-primary' : 'text-text-muted'"
                        aria-hidden="true"
                    >
                        {{ item.icon }}
                    </span>
                    <span class="text-[11px] font-semibold tracking-tight">{{ item.name }}</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
