<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Search, Phone, ChevronDown, User } from '@lucide/vue';

const page = usePage();
const user = page.props.auth?.user;

const selectedLanguage = ref('EN');
const selectedCurrency = ref('USD');
const isLangOpen = ref(false);
const isCurrOpen = ref(false);

const languages = ['EN', 'FR', 'SW', 'ES', 'DE'];
const currencies = ['USD', 'EUR', 'GBP', 'TZS', 'KES'];
</script>

<template>
    <div class="border-b border-zinc-100 bg-white text-xs text-zinc-600 transition-colors dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6 lg:px-8">
            <!-- Left side: Language, Currency, Quick Search -->
            <div class="flex items-center space-x-5">
                <!-- Language Selector -->
                <div class="relative">
                    <button 
                        @click="isLangOpen = !isLangOpen; isCurrOpen = false" 
                        class="flex items-center gap-1 font-medium transition hover:text-red-500"
                        type="button"
                    >
                        <span>{{ selectedLanguage }}</span>
                        <ChevronDown class="h-3.5 w-3.5 text-zinc-400" />
                    </button>
                    <div 
                        v-if="isLangOpen" 
                        class="absolute left-0 top-full z-50 mt-1 min-w-[80px] rounded-md border border-zinc-100 bg-white py-1 shadow-lg dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        <button 
                            v-for="lang in languages" 
                            :key="lang"
                            @click="selectedLanguage = lang; isLangOpen = false"
                            class="block w-full px-3 py-1 text-left text-xs hover:bg-red-50 hover:text-red-600 dark:hover:bg-zinc-800"
                        >
                            {{ lang }}
                        </button>
                    </div>
                </div>

                <div class="h-3 w-px bg-zinc-200 dark:bg-zinc-800"></div>

                <!-- Currency Selector -->
                <div class="relative">
                    <button 
                        @click="isCurrOpen = !isCurrOpen; isLangOpen = false" 
                        class="flex items-center gap-1 font-medium transition hover:text-red-500"
                        type="button"
                    >
                        <span>{{ selectedCurrency }}</span>
                        <ChevronDown class="h-3.5 w-3.5 text-zinc-400" />
                    </button>
                    <div 
                        v-if="isCurrOpen" 
                        class="absolute left-0 top-full z-50 mt-1 min-w-[80px] rounded-md border border-zinc-100 bg-white py-1 shadow-lg dark:border-zinc-800 dark:bg-zinc-900"
                    >
                        <button 
                            v-for="curr in currencies" 
                            :key="curr"
                            @click="selectedCurrency = curr; isCurrOpen = false"
                            class="block w-full px-3 py-1 text-left text-xs hover:bg-red-50 hover:text-red-600 dark:hover:bg-zinc-800"
                        >
                            {{ curr }}
                        </button>
                    </div>
                </div>

                <div class="hidden h-3 w-px bg-zinc-200 sm:block dark:bg-zinc-800"></div>

                <!-- Quick Search Icon -->
                <a href="#explore" class="hidden items-center gap-1.5 text-zinc-500 transition hover:text-red-500 sm:flex">
                    <Search class="h-3.5 w-3.5" />
                    <span>Quick Browse</span>
                </a>
            </div>

            <!-- Right side: Phone & Authentication Links -->
            <div class="flex items-center space-x-5">
                <a href="tel:+12227776565" class="hidden items-center gap-1.5 transition hover:text-red-500 sm:flex">
                    <Phone class="h-3.5 w-3.5 text-red-500" />
                    <span>+1 222 777 6565</span>
                </a>

                <div class="hidden h-3 w-px bg-zinc-200 sm:block dark:bg-zinc-800"></div>

                <template v-if="user">
                    <Link 
                        href="/dashboard" 
                        class="flex items-center gap-1.5 font-semibold text-zinc-900 transition hover:text-red-500 dark:text-zinc-100"
                    >
                        <User class="h-3.5 w-3.5 text-red-500" />
                        <span>Dashboard ({{ user.name }})</span>
                    </Link>
                </template>
                <template v-else>
                    <Link 
                        href="/login" 
                        class="transition hover:text-red-500"
                    >
                        Sign In
                    </Link>
                    <span class="text-zinc-300 dark:text-zinc-700">/</span>
                    <Link 
                        href="/register" 
                        class="font-semibold text-red-500 transition hover:text-red-600"
                    >
                        Register
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
