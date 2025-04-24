<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { getCookie, setCookie, removeCookie } from '@/utils/auth';
import { Calendar, User, Moon, Sun, Clock, LogOut } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

import MakeReservation from './ClientDashboard/MakeReservation.vue';
import MyReservations from './ClientDashboard/MyReservations.vue';
import ProfileSettings from './ClientDashboard/ProfileSettings.vue';
import AppearanceSettings from './ClientDashboard/AppearanceSettings.vue';
import { Client, Country } from '@/interfaces/model.interface';

interface PageProps {
    flash: {
        token?: string;
        token_type?: string;
        [key: string]: any;
    };
    [key: string]: any;
}

const page = usePage<PageProps>();
const client = ref<Client | null>(null);
const countries = ref<Country[]>([]);
const activeTab = ref('make-reservation');
const theme = ref(localStorage.getItem('theme') || 'system');

// Get current route
const getCurrentTab = () => {
    const path = window.location.pathname;
    if (path.includes('/client/my-reservations')) return 'my-reservations';
    if (path.includes('/client/profile')) return 'profile';
    if (path.includes('/client/appearance')) return 'appearance';
    return 'make-reservation'; // Default
};

const fetchClientData = async () => {
  try {
    const response = await fetch('/api/client/me', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${getCookie('client_token')}`
      }
    });

    if (response.ok) {
      client.value = await response.json();
    } else {
      router.visit('/login');
    }
  } catch (error) {
    router.visit('/login');
  }
};

onMounted(() => {
    if (page.props.countries) {
        countries.value = page.props.countries.data;
    }

    const token = page.props.token;
    if (token) {
        setCookie('client_token', token);
        router.visit('/client/dashboard');
    }

    fetchClientData();
    applyTheme(theme.value);

    // Set active tab based on current route
    activeTab.value = getCurrentTab();
});

// Change URL when tab changes
const changeTab = (tab: string) => {
    activeTab.value = tab;

    // Update URL based on tab
    let url = '/client/';
    switch(tab) {
        case 'make-reservation':
            url += 'make-reservation';
            break;
        case 'my-reservations':
            url += 'my-reservations';
            break;
        case 'profile':
            url += 'profile';
            break;
        case 'appearance':
            url += 'appearance';
            break;
    }

    router.visit(url, { preserveState: true });
};

const applyTheme = (selectedTheme: string) => {
    theme.value = selectedTheme;
    localStorage.setItem('theme', selectedTheme);

    const root = window.document.documentElement;
    root.classList.remove('light', 'dark');

    if (selectedTheme === 'system') {
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        root.classList.add(systemTheme);
    } else {
        root.classList.add(selectedTheme);
    }
};

const handleLogout = () => {
    router.post('/client/logout', {}, {
        onSuccess: () => {
            removeCookie('client_token');
        }
    });
};
</script>

<template>
    <Head title="Client Dashboard" />

    <div class="flex h-screen bg-background">
        <!-- Sidebar -->
        <div class="w-1/5 bg-card border-r border-border p-4 flex flex-col">
            <div v-if="client" class="mb-6">
                <div class="flex items-center space-x-4 mb-6">
                    <Avatar class="w-12 h-12">
                        <AvatarImage :src="client.avatar_image" :alt="client.name" />
                        <AvatarFallback>{{ client.name.charAt(0) }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <h2 class="font-semibold">{{ client.name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ client.email }}</p>
                    </div>
                </div>
                <Separator class="mb-4" />
            </div>

            <nav class="flex-1 space-y-2">
                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'make-reservation' }"
                    @click="changeTab('make-reservation')">
                    <Calendar class="mr-2 h-4 w-4" />
                    Make Reservation
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'my-reservations' }"
                    @click="changeTab('my-reservations')">
                    <Clock class="mr-2 h-4 w-4" />
                    My Reservations
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'profile' }"
                    @click="changeTab('profile')">
                    <User class="mr-2 h-4 w-4" />
                    Profile
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'appearance' }"
                    @click="changeTab('appearance')">
                    <Sun class="mr-2 h-4 w-4" />
                    Appearance
                </Button>
            </nav>

            <Button variant="destructive" class="w-full mt-auto" @click="handleLogout">
                <LogOut class="mr-2 h-4 w-4" />
                Logout
            </Button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            <!-- Make Reservation Tab -->
            <MakeReservation v-if="activeTab === 'make-reservation'" />

            <!-- My Reservations Tab -->
            <MyReservations v-if="activeTab === 'my-reservations'" />

            <!-- Profile Tab -->
            <ProfileSettings
                v-if="activeTab === 'profile' && client"
                :client="client"
                :countries="countries"
                @client-updated="fetchClientData"
            />

            <!-- Appearance Tab -->
            <AppearanceSettings
                v-if="activeTab === 'appearance'"
                :theme="theme"
                @updateTheme="applyTheme"
            />
        </div>
    </div>
</template>
