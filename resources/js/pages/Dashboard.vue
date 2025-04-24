<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ArcElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { computed, onMounted, ref, watch } from 'vue';
import { Line, Pie } from 'vue-chartjs';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Title, Tooltip, Legend);

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

// Chart data and loading state
const loading = ref(true);
const statisticsData = ref({
    totalClients: { totalMales: 0, totalFemales: 0, total: 0 },
    monthlyRevenue: Array(12).fill(0),
    reservationsByCountry: [],
    topClients: [],
});

// Chart options
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right' as const,
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.label || '';
                    if (label) {
                        label += ': ';
                    }
                    if (context.dataset.label === 'Revenue') {
                        label += '$' + context.raw.toLocaleString();
                    } else {
                        label += context.raw;
                    }
                    return label;
                },
            },
        },
    },
};

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    return '$' + context.raw.toLocaleString();
                },
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function (value) {
                    return '$' + value;
                },
            },
        },
    },
};

// Computed chart data
const genderChartData = computed(() => ({
    labels: ['Male', 'Female'],
    datasets: [
        {
            data: [statisticsData.value.totalClients.totalMales, statisticsData.value.totalClients.totalFemales],
            backgroundColor: ['#3b82f6', '#ec4899'],
            borderWidth: 1,
        },
    ],
}));

const revenueChartData = computed(() => ({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
        {
            label: 'Revenue',
            data: statisticsData.value.monthlyRevenue,
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: '#10b981',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
        },
    ],
}));

const countriesChartData = computed(() => ({
    labels: statisticsData.value.reservationsByCountry.map((item) => item.country),
    datasets: [
        {
            data: statisticsData.value.reservationsByCountry.map((item) => item.reservations),
            backgroundColor: ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#6366f1', '#8b5cf6', '#14b8a6', '#f97316', '#0ea5e9', '#06b6d4'],
            borderWidth: 1,
        },
    ],
}));

const topClientsChartData = computed(() => ({
    labels: statisticsData.value.topClients.map((item) => item.name),
    datasets: [
        {
            data: statisticsData.value.topClients.map((item) => item.reservations),
            backgroundColor: ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#6366f1', '#8b5cf6', '#14b8a6', '#f97316', '#0ea5e9', '#06b6d4'],
            borderWidth: 1,
        },
    ],
}));

// Computed statistics summaries
const malePercentage = computed(() => {
    const total = statisticsData.value.totalClients.total;
    return total > 0 ? Math.round((statisticsData.value.totalClients.totalMales / total) * 100) : 0;
});

const femalePercentage = computed(() => {
    const total = statisticsData.value.totalClients.total;
    return total > 0 ? Math.round((statisticsData.value.totalClients.totalFemales / total) * 100) : 0;
});

const totalRevenue = computed(() => {
    return statisticsData.value.monthlyRevenue.reduce((sum, val) => sum + val, 0);
});

const peakRevenueMonth = computed(() => {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const maxRevenue = Math.max(...statisticsData.value.monthlyRevenue);
    const monthIndex = statisticsData.value.monthlyRevenue.indexOf(maxRevenue);
    return {
        month: months[monthIndex],
        value: maxRevenue,
    };
});

const topCountry = computed(() => {
    return statisticsData.value.reservationsByCountry[0] || { country: 'N/A', reservations: 0 };
});

const topClient = computed(() => {
    return statisticsData.value.topClients[0] || { name: 'N/A', reservations: 0 };
});

// Fetch data from API
const fetchStatistics = async () => {
    loading.value = true;
    try {
        const response = await axios.get('http://127.0.0.1:8000/dashboard-statistics', {
            params: { year: selectedYear.value },
        });
        statisticsData.value = response.data;
    } catch (error) {
        console.error('Error fetching statistics:', error);
    } finally {
        loading.value = false;
    }
};

// Watch for year changes
watch(selectedYear, () => {
    fetchStatistics();
});

// Initial data fetch
onMounted(() => {
    fetchStatistics();
});
</script>

<template>
    <Head title="Statistics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="statistics-page">
            <div class="export-filter-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px">
                <!-- Year Filter Dropdown on Left -->
                <div class="year-filter" style="margin-bottom: 15px">
                    <label for="year-select" style="margin-right: 10px">Filter by year:</label>
                    <select
                        id="year-select"
                        v-model="selectedYear"
                        class="form-select"
                        style="padding: 8px; border-radius: 4px; border: 1px solid #ccc"
                    >
                        <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                    </select>
                </div>

                <!-- Export Button on Right -->
                <a
                    :href="route('clients.export')"
                    class="export-btn"
                    style="
                        display: flex;
                        align-items: center;
                        background-color: #4caf50;
                        color: white;
                        padding: 8px 16px;
                        text-decoration: none;
                        border-radius: 4px;
                    "
                >
                    <i class="download-icon" style="margin-right: 8px"></i> Export Client Data
                </a>
            </div>

            <div v-if="loading" class="loading-state">
                <div class="loader"></div>
                <p>Loading statistics data...</p>
            </div>

            <div v-else class="charts-container">
                <div class="charts-grid">
                    <!-- Gender Distribution Chart -->
                    <div class="chart-card">
                        <h3 class="chart-title">Gender Distribution</h3>
                        <div class="chart-wrapper">
                            <Pie :data="genderChartData" :options="chartOptions" />
                        </div>
                        <div class="chart-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total Clients:</span>
                                <span class="summary-value">{{ statisticsData.totalClients.total }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Male:</span>
                                <span class="summary-value"> {{ statisticsData.totalClients.totalMales }} ({{ malePercentage }}%) </span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Female:</span>
                                <span class="summary-value"> {{ statisticsData.totalClients.totalFemales }} ({{ femalePercentage }}%) </span>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Revenue Chart -->
                    <div class="chart-card">
                        <h3 class="chart-title">Monthly Revenue</h3>
                        <div class="chart-wrapper">
                            <Line :data="revenueChartData" :options="lineChartOptions" />
                        </div>
                        <div class="chart-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total Revenue:</span>
                                <span class="summary-value">${{ totalRevenue.toLocaleString() }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Peak Month:</span>
                                <span class="summary-value"> {{ peakRevenueMonth.month }} (${{ peakRevenueMonth.value.toLocaleString() }}) </span>
                            </div>
                        </div>
                    </div>

                    <!-- Reservations by Country -->
                    <div class="chart-card">
                        <h3 class="chart-title">Reservations by Country</h3>
                        <div class="chart-wrapper">
                            <Pie :data="countriesChartData" :options="chartOptions" />
                        </div>
                        <div class="chart-summary">
                            <div class="summary-item">
                                <span class="summary-label">Total Countries:</span>
                                <span class="summary-value">{{ statisticsData.reservationsByCountry.length }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Top Country:</span>
                                <span class="summary-value"> {{ topCountry.country }} ({{ topCountry.reservations }} reservations) </span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Clients -->
                    <div class="chart-card">
                        <h3 class="chart-title">Top Clients by Reservations</h3>
                        <div class="chart-wrapper">
                            <Pie :data="topClientsChartData" :options="chartOptions" />
                        </div>
                        <div class="chart-summary">
                            <div class="summary-item">
                                <span class="summary-label">Top Client:</span>
                                <span class="summary-value"> {{ topClient.name }} ({{ topClient.reservations }} reservations) </span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Total Shown:</span>
                                <span class="summary-value">{{ statisticsData.topClients.length }} clients</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.statistics-page {
    padding: 1.5rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.year-filter {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.year-filter label {
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

.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 300px;
}

.loader {
    border: 4px solid #f3f3f3;
    border-radius: 50%;
    border-top: 4px solid #e0b472;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #0b1626;
    margin-bottom: 2rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.chart-card {
    background: #f9fafb;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.chart-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0b1626;
    margin-bottom: 1rem;
    text-align: center;
}

.chart-wrapper {
    height: 300px;
    position: relative;
}

.chart-summary {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eaeaea;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.summary-label {
    font-weight: 500;
    color: #6b7280;
}

.summary-value {
    font-weight: 600;
    color: #0b1626;
}

@media (max-width: 1024px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .year-filter {
        flex-direction: column;
        align-items: flex-start;
    }

    .form-select {
        width: 100%;
    }

    .summary-item {
        flex-direction: column;
    }

    .summary-value {
        margin-top: 0.25rem;
    }
}
</style>
