<script setup lang="ts">
import { Sun, Moon, Monitor } from 'lucide-vue-next';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    theme: string;
}>();

const emit = defineEmits(['updateTheme']);

const applyTheme = (selectedTheme: string) => {
    emit('updateTheme', selectedTheme);
};
</script>

<template>
    <h1 class="text-2xl font-bold mb-6">Appearance Settings</h1>
    <Card>
        <CardHeader>
            <CardTitle>Theme</CardTitle>
            <CardDescription>Choose your preferred theme</CardDescription>
        </CardHeader>
        <CardContent>
            <div class="grid grid-cols-3 gap-4">
                <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                    :class="{ 'border-primary': props.theme === 'light' }" @click="applyTheme('light')">
                    <Sun class="h-8 w-8 mb-2" />
                    <span>Light</span>
                </Button>
                <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                    :class="{ 'border-primary': props.theme === 'dark' }" @click="applyTheme('dark')">
                    <Moon class="h-8 w-8 mb-2" />
                    <span>Dark</span>
                </Button>
                <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                    :class="{ 'border-primary': props.theme === 'system' }" @click="applyTheme('system')">
                    <Monitor class="h-8 w-8 mb-2" />
                    <span>System</span>
                </Button>
            </div>
        </CardContent>
    </Card>
</template>