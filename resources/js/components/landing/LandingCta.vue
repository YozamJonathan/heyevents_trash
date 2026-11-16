<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { register, dashboard } from '@/routes';
import { ArrowRight } from '@lucide/vue';

const page = usePage();
</script>

<template>
    <section class="py-16 lg:py-24">
        <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-zinc-950 px-8 py-16 text-white shadow-2xl dark:bg-zinc-900 sm:px-16">
                <!-- Subtle glow -->
                <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl" />

                <h2 class="text-3xl font-black tracking-tight sm:text-4xl">
                    Ready to elevate your <span class="font-medium italic text-zinc-400">next event?</span>
                </h2>

                <p class="mx-auto mt-4 max-w-lg text-sm font-medium text-zinc-400">
                    Join thousands of organizers creating seamless, memorable experiences with eSherehe.
                </p>

                <div class="mt-8 flex justify-center">
                    <Link
                        :href="page.props.auth?.user ? dashboard() : register()"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-8 py-3.5 text-xs font-semibold text-zinc-950 shadow-md transition-all hover:bg-zinc-100 hover:shadow-lg active:scale-95"
                    >
                        <span>{{ page.props.auth?.user ? 'Open Your Dashboard' : 'Get Started for Free' }}</span>
                        <ArrowRight class="h-3.5 w-3.5" />
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
