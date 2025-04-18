<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: 'Male',
    country_id: null,
    mobile: '',
    avatar_image: null,
});
interface Country {
  id: number;
  name: string;
  iso_alpha_2: string;
}

const props = defineProps<{
  countries: Country[];
}>();

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label for="gender">Gender</Label>
                    <Select v-model="form.gender" required>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select gender" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectItem value="Male">Male</SelectItem>
                            <SelectItem value="Female">Female</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.gender" />
                </div>

                <div class="grid gap-2">
                    <Label for="country">Country</Label>
                    <Select v-model="form.country_id" required>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select country" />
                        </SelectTrigger>
                        <SelectContent class="w-full">
                            <SelectItem
                                v-for="country in countries"
                                :key="country.id"
                                :value="country.id"
                            >
                                {{ country.name }} ({{ country.iso_alpha_2 }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.country_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="mobile">Mobile Number</Label>
                    <Input
                        id="mobile"
                        type="tel"
                        required
                        v-model="form.mobile"
                        placeholder="+20 123 456 7890"
                    />
                    <InputError :message="form.errors.mobile" />
                </div>

                <div class="grid gap-2">
                    <Label for="avatar_image">Profile Picture</Label>
                    <Input
                        id="avatar_image"
                        type="file"
                        required
                        accept="image/jpeg,image/png,image/jpg"
                        @input="form.avatar_image = $event.target.files[0]"
                    />
                    <InputError :message="form.errors.avatar_image" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
