<script setup lang="ts">
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { Calendar } from 'lucide-vue-next';
</script>

<template>
    <h1 class="text-2xl font-bold mb-6">Make Reservation</h1>
    <Alert>
        <Calendar class="h-4 w-4" />
        <AlertTitle>Coming Soon!</AlertTitle>
        <AlertDescription>
            Room reservation feature will be available soon. We're finishing up the final touches!
        </AlertDescription>
    </Alert>
</template>