<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref, onMounted } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Generate available years (current year and 5 years back)
const currentYear = new Date().getFullYear();
const availableYears = Array.from({ length: 6 }, (_, i) => currentYear - i);
const selectedYear = ref(currentYear);

// These values would typically be fetched from your backend
const totalClients = ref(null);
const yearlyBookings = ref(null);
const totalRevenue = ref(null);

onMounted(() => {
  // Placeholder for fetching data based on selected year
  // fetchDashboardData(selectedYear.value);
});

// Example of a method to fetch data when year changes
// watch(selectedYear, (newYear) => {
//   fetchDashboardData(newYear);
// });
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="true" class="dashboard-controls">
            <div class="export-filter-row">
                <div class="year-selector">
                    <label for="year-select">Filter by year:</label>
                    <select id="year-select" v-model="selectedYear" class="form-select">
                        <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>

                <a :href="route('clients.export')" class="export-btn">
                    <i class="download-icon"></i> Export Client Data
                </a>
            </div>

            <div class="dashboard-placeholders">
                <div class="placeholder-card">
                    <h3>Total Clients</h3>
                    <div class="placeholder-value">{{ totalClients || '0' }}</div>
                </div>

                <div class="placeholder-card">
                    <h3>Bookings This Year</h3>
                    <div class="placeholder-value">{{ yearlyBookings || '0' }}</div>
                </div>

                <div class="placeholder-card">
                    <h3>Revenue</h3>
                    <div class="placeholder-value">${{ totalRevenue || '0' }}</div>
                </div>
            </div>
        </div>

        <div v-else class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>

    </AppLayout>
</template>

<style scoped>
.dashboard-controls {
    margin: 1.5rem 0;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
}

.export-filter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eaeaea;
}

.year-selector {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.year-selector label {
    font-weight: 500;
    color: #444;
}

.form-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: white;
    min-width: 120px;
    font-size: 0.875rem;
}

.export-btn {
    display: flex;
    align-items: center;
    background-color: #e0b472;
    color: #0b1626;
    padding: 0.5rem 1.25rem;
    border-radius: 0.375rem;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.2s;
}

.export-btn:hover {
    background-color: #d4aa69;
}

.download-icon {
    display: inline-block;
    width: 16px;
    height: 16px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4'/%3E%3Cpolyline points='7 10 12 15 17 10'/%3E%3Cline x1='12' y1='15' x2='12' y2='3'/%3E%3C/svg%3E");
    margin-right: 0.5rem;
}

.dashboard-placeholders {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.placeholder-card {
    background: #f9fafb;
    border: 1px solid #eaeaea;
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
}

.placeholder-card h3 {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
    font-weight: 500;
}

.placeholder-value {
    font-size: 1.875rem;
    font-weight: 600;
    color: #0b1626;
}
</style>

