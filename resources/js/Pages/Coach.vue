<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../Layouts/AppLayout.vue';

defineProps<{
    appName: string;
}>();

interface ChatMessage {
    id: number;
    sender: 'kiko' | 'user';
    text: string;
    timestamp: string;
}

const messages = ref<ChatMessage[]>([
    {
        id: 1,
        sender: 'kiko',
        text: 'Halo! Saya Kiko, AI Debate Coach Anda. Ingin latihan brainstorming mosi, membedah struktur AREL, atau sparring argumen hari ini?',
        timestamp: '10:00',
    },
]);

const userInput = ref('');

const samplePrompts = [
    'Bantu bedah mosi: Dewan ini akan melarang deepfake AI di pemilu',
    'Bagaimana cara membedah argumen Opening Opposition di BP?',
    'Jelaskan cara membuat comparative weighing yang meyakinkan',
];

const sendMessage = () => {
    const text = userInput.value.trim();
    if (!text) return;

    messages.value.push({
        id: Date.now(),
        sender: 'user',
        text,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    });

    userInput.value = '';

    // Mock response from Kiko
    setTimeout(() => {
        messages.value.push({
            id: Date.now() + 1,
            sender: 'kiko',
            text: `Catatan dari Kiko: Terkait poin "${text.slice(0, 35)}...", pastikan kamu selalu menyusun mekanisme kausalitas (mengapa X menyebabkan Y) dan perjelas 'burden of proof' pihakmu!`,
            timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        });
    }, 600);
};

const usePrompt = (promptText: string) => {
    userInput.value = promptText;
};
</script>

<template>
    <Head title="Coach Kiko — AI Debate Coach" />

    <AppLayout>
        <div class="flex flex-col gap-6 max-w-4xl mx-auto h-[calc(100vh-10rem)] min-h-[550px]">
            <!-- Header -->
            <header class="flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-2xl bg-primary-light flex items-center justify-center overflow-hidden border border-primary-container/20 shadow-xs">
                        <img
                            src="/images/maskot.png"
                            alt="Coach Kiko Mascot"
                            class="w-10 h-10 object-contain"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                        />
                        <span class="material-symbols-outlined text-primary text-2xl hidden" aria-hidden="true">smart_toy</span>
                        <span class="absolute bottom-1 right-1 w-2.5 h-2.5 rounded-full bg-primary-container ring-2 ring-surface-white" title="Online"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-on-surface font-serif">Coach Kiko</h1>
                            <span class="px-2 py-0.5 rounded-full bg-primary-light text-primary text-[10px] font-extrabold uppercase tracking-wide">
                                AI Mentor
                            </span>
                        </div>
                        <p class="text-xs text-text-muted">Siap membedah argumen, roleplay sparring, dan evaluasi logika AREL</p>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-text-muted">
                    <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse" aria-hidden="true"></span>
                    <span>Aktif &amp; Siap Diskusi</span>
                </div>
            </header>

            <!-- Chat Window -->
            <div class="flex-1 bg-surface-white rounded-3xl border border-border-clean/60 shadow-sm flex flex-col overflow-hidden">
                <!-- Messages List -->
                <div class="flex-1 p-4 sm:p-6 overflow-y-auto flex flex-col gap-4">
                    <div
                        v-for="msg in messages"
                        :key="msg.id"
                        class="flex gap-3 max-w-[85%] sm:max-w-[75%]"
                        :class="msg.sender === 'user' ? 'ml-auto flex-row-reverse' : ''"
                    >
                        <div
                            v-if="msg.sender === 'kiko'"
                            class="w-8 h-8 rounded-full bg-primary-light flex items-center justify-center shrink-0 border border-primary-container/20 text-primary font-bold text-xs"
                        >
                            K
                        </div>

                        <div
                            class="p-4 rounded-3xl text-sm leading-relaxed"
                            :class="[
                                msg.sender === 'user'
                                    ? 'bg-primary-container text-white rounded-tr-xs shadow-xs'
                                    : 'bg-surface-subtle text-on-surface rounded-tl-xs border border-border-clean/60'
                            ]"
                        >
                            <p>{{ msg.text }}</p>
                            <span
                                class="text-[10px] mt-1.5 block opacity-70"
                                :class="msg.sender === 'user' ? 'text-right text-white/80' : 'text-text-muted'"
                            >
                                {{ msg.timestamp }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sample Prompts Carousel / Pills -->
                <div class="px-4 py-2 border-t border-border-clean/50 bg-background-cream/40 flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <span class="text-[11px] font-bold text-text-muted whitespace-nowrap shrink-0">Contoh Topik:</span>
                    <button
                        v-for="(prompt, idx) in samplePrompts"
                        :key="idx"
                        type="button"
                        class="text-xs px-3 py-1.5 rounded-full bg-surface-white border border-border-clean text-on-surface hover:border-primary hover:text-primary transition-all whitespace-nowrap shrink-0 min-h-[36px] focus-visible:outline-2 focus-visible:outline-primary-container"
                        @click="usePrompt(prompt)"
                    >
                        {{ prompt }}
                    </button>
                </div>

                <!-- Input Footer -->
                <form
                    class="p-3 sm:p-4 border-t border-border-clean/60 bg-surface-white flex items-center gap-2"
                    @submit.prevent="sendMessage"
                >
                    <label for="coach-message-input" class="sr-only">Ketik pertanyaan atau argumen Anda untuk Coach Kiko</label>
                    <input
                        id="coach-message-input"
                        v-model="userInput"
                        type="text"
                        placeholder="Ketik pertanyaan mosi atau draf argumen Anda..."
                        class="flex-1 px-4 py-3 rounded-2xl bg-surface-subtle border border-border-clean text-sm text-on-surface placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary-container min-h-[44px]"
                    />
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-5 py-3 rounded-2xl bg-primary-container text-white text-sm font-bold shadow-xs hover:bg-primary transition-all min-h-[44px] min-w-[44px] focus-visible:outline-2 focus-visible:outline-primary-container shrink-0"
                    >
                        <span class="material-symbols-outlined text-lg" aria-hidden="true">send</span>
                        <span class="hidden sm:inline">Kirim</span>
                    </button>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
