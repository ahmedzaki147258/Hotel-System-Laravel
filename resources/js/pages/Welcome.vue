<script setup lang="ts">
import { isAuthenticated } from '@/utils/auth';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import type { PageProps as InertiaPageProps } from '@inertiajs/core';

// Get page props with proper typing
interface PageProps extends InertiaPageProps {
  auth: {
    user: any;
  };
}
const page = usePage<PageProps>();

// Auto-redirect authenticated users to dashboard
onMounted(() => {
    if (isAuthenticated()) {
        router.visit('/client/dashboard');
    }
})
</script>

<template>
    <Head title="Welcome to Luxury Hotel">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    </Head>

    <div class="min-h-screen bg-[#0b1626] text-white overflow-hidden">
        <!-- Static Hotel Background -->
        <div class="absolute inset-0 z-0 opacity-30">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center"></div>
        </div>

        <!-- Static Glow Effects -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-20"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-15"></div>
        <div class="absolute bottom-1/4 right-1/3 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-[128px] opacity-15"></div>

        <!-- Header -->
        <header class="relative z-10 flex items-center justify-between px-8 py-6 lg:px-16">
            <div class="flex items-center">
                <div class="font-playfair text-3xl font-bold text-white tracking-wider">
                    <span class="text-[#e0b472]">LUXE</span>HOTEL
                </div>
            </div>
            <nav class="flex items-center space-x-3 md:space-x-6">
                <template v-if="page.props.auth.user">
                    <Link
                        :href="route('dashboard')"
                        class="px-5 py-2 text-sm font-medium transition-colors rounded-md hover:bg-[#e0b472]/10 border border-[#e0b472]/20 hover:border-[#e0b472]/50"
                    >
                        Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-6 py-2.5 text-sm font-medium transition-colors hover:text-[#e0b472]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="px-6 py-2.5 text-sm font-medium text-[#0b1626] bg-[#e0b472] hover:bg-[#d4aa69] transition-colors rounded-md"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 px-8 pt-12 lg:px-16 lg:pt-16">
            <div class="flex flex-col-reverse lg:flex-row lg:items-center lg:justify-between">
                <!-- Left Content -->
                <div class="mt-8 lg:mt-0 lg:w-1/2">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-playfair font-bold mb-6 leading-tight">
                        Experience <span class="text-[#e0b472]">Luxury</span> Like Never Before
                    </h1>
                    <p class="text-lg text-gray-300 mb-8 max-w-xl">
                        Discover the perfect blend of comfort, elegance, and world-class service. Your unforgettable stay begins here.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-12">
                        <dev
                            class="px-8 py-3 text-center bg-[#e0b472] text-[#0b1626] rounded-md font-medium hover:bg-[#d4aa69] transition-colors"
                        >
                            Book Now
                        </dev>
                        <dev
                            class="px-8 py-3 text-center border border-[#e0b472]/50 text-white rounded-md font-medium hover:bg-[#e0b472]/10 transition-colors"
                        >
                            Explore Rooms
                        </dev>
                    </div>

                    <!-- Hotel Features -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="flex items-start space-x-3">
                            <div class="p-2 rounded-full bg-[#e0b472]/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e0b472]" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">5-Star Luxury</h3>
                                <p class="text-sm text-gray-400">Best-in-class comfort</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="p-2 rounded-full bg-[#e0b472]/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e0b472]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Prime Location</h3>
                                <p class="text-sm text-gray-400">Central to attractions</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="p-2 rounded-full bg-[#e0b472]/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e0b472]" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Personalized Service</h3>
                                <p class="text-sm text-gray-400">24/7 concierge</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="p-2 rounded-full bg-[#e0b472]/10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#e0b472]" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium">Easy Booking</h3>
                                <p class="text-sm text-gray-400">Online system</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Hotel Image -->
                <div class="relative lg:w-1/2">
                    <div class="relative mx-auto w-full max-w-lg">
                        <!-- Main image -->
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/50 border border-gray-700/60 aspect-[4/3]">
                            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Luxury Hotel Suite" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-white font-medium">Presidential Suite</h3>
                                        <p class="text-gray-300 text-sm">Exclusive experience</p>
                                    </div>
                                    <div class="px-3 py-1.5 bg-[#e0b472] text-[#0b1626] rounded-lg font-medium">
                                        From $399
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style>
.font-playfair {
    font-family: 'Playfair Display', serif;
}

:root {
    font-family: 'Poppins', sans-serif;
}
</style>
