<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { isAuthenticated, setCookie } from '@/utils/auth';

interface PageProps {
    flash: {
        token?: string;
        token_type?: string;
        [key: string]: any;
    };
    [key: string]: any;
}

const page = usePage<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Client Dashboard',
        href: '/client/dashboard',
    },
];

onMounted(() => {
    const token = page.props.token;
    const tokenType = page.props.token_type;

    if (token) {
        setCookie('client_token', `${tokenType || 'Bearer'} ${token}`);
        router.visit('/client/dashboard');
    } else if (!isAuthenticated()) {
        router.visit('/login');
    }
});
</script>

<template>
    <Head title="Client Dashboard" />

    <p>Client Dashboard</p>
</template>
