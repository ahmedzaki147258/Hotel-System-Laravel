<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center relative overflow-hidden">
        <!-- Static Background -->
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')] bg-cover bg-center"></div>
                <div class="absolute inset-0 bg-[#0b1626]/75 backdrop-blur-sm"></div>
            </div>
        </div>

        <!-- Static decorative elements -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-20"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-15"></div>

        <!-- Main content -->
        <div class="w-full max-w-md z-10 p-8">
            <div class="flex flex-col gap-8">
                <!-- Logo and header -->
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="flex items-center justify-center">
                            <div class="font-serif text-3xl font-bold text-white tracking-wider">
                                <span class="text-[#e0b472]">LUXE</span>HOTEL
                            </div>
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-2xl font-medium text-white">{{ title }}</h1>
                        <p class="text-center text-sm text-gray-300">{{ description }}</p>
                    </div>
                </div>

                <!-- Auth form container with glass effect -->
                <div class="auth-form-container bg-white/10 backdrop-blur-md p-8 rounded-xl border border-white/20 shadow-xl">
                    <slot />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-4 text-center text-white/50 text-xs z-10">
            &copy; {{ new Date().getFullYear() }} LUXE HOTEL. All rights reserved.
        </div>
    </div>
</template>

<style>
/* No animations */
</style>
