<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Mail, Lock, User, Building } from 'lucide-vue-next';

const page = usePage();

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    userType: 'client',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthBase title="Welcome Back" description="Enter your credentials to access your account">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-400 bg-green-400/10 p-3 rounded-lg">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <!-- Hidden CSRF token field -->
            <input type="hidden" name="_token" :value="page.props.csrf_token">

            <div class="grid gap-6">
                <div class="grid gap-3">
                    <Label for="email" class="text-white flex items-center gap-2">
                        <Mail class="h-4 w-4 text-[#e0b472]" />
                        Email address
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.email" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <div class="flex items-center justify-between">
                        <Label for="password" class="text-white flex items-center gap-2">
                            <Lock class="h-4 w-4 text-[#e0b472]" />
                            Password
                        </Label>
                        <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-[#e0b472] hover:text-[#e0b472]/80" :tabindex="5">
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.password" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <Label class="text-white flex items-center gap-2">
                        <User class="h-4 w-4 text-[#e0b472]" />
                        Login as:
                    </Label>

                    <div class="flex items-center space-x-2 p-2 bg-white/5 rounded-lg border border-white/10">
                        <label class="relative flex-1">
                            <input
                                type="radio"
                                name="userType"
                                value="client"
                                v-model="form.userType"
                                class="peer sr-only"
                            />
                            <div class="flex items-center justify-center p-3 rounded-md border border-white/20 peer-checked:bg-[#e0b472]/20 peer-checked:border-[#e0b472] transition-all cursor-pointer text-gray-300 peer-checked:text-white">
                                <User class="h-4 w-4 mr-2" />
                                Client
                            </div>
                        </label>

                        <label class="relative flex-1">
                            <input
                                type="radio"
                                name="userType"
                                value="administration"
                                v-model="form.userType"
                                class="peer sr-only"
                            />
                            <div class="flex items-center justify-center p-3 rounded-md border border-white/20 peer-checked:bg-[#e0b472]/20 peer-checked:border-[#e0b472] transition-all cursor-pointer text-gray-300 peer-checked:text-white">
                                <Building class="h-4 w-4 mr-2" />
                                Administration
                            </div>
                        </label>
                    </div>
                    <InputError :message="form.errors.userType" class="text-red-300" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full bg-[#e0b472] hover:bg-[#d4aa69] text-[#0b1626] font-medium"
                    :tabindex="4"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Log in
                </Button>
            </div>

            <div class="text-center text-sm text-gray-300">
                Don't have an account?
                <TextLink :href="route('register')" class="text-[#e0b472] hover:text-[#e0b472]/80" :tabindex="5">
                    Sign up
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
