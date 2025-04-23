<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, User, Mail, Lock, MapPin, Phone, Upload, Users, X, AlertCircle } from 'lucide-vue-next';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref, watch, computed } from 'vue';

const form = useForm<{
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    gender: string;
    country_id: number | null;
    mobile: string;
    avatar_image: File | null;
}>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    gender: 'Male',
    country_id: null,
    mobile: '',
    avatar_image: null,
});

// Add ref for image preview
const imagePreview = ref<string | null>(null);
const formSubmitted = ref(false);
const imageValidationError = computed(() => {
    return formSubmitted.value && !form.avatar_image;
});

// Handle image selection and generate preview
const handleImageSelection = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.avatar_image = file;

        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Clear selected image
const clearImage = () => {
    form.avatar_image = null;
    imagePreview.value = null;

    // Reset file input
    const fileInput = document.getElementById('avatar_image') as HTMLInputElement;
    if (fileInput) {
        fileInput.value = '';
    }
};

interface Country {
  id: number;
  name: string;
  iso_alpha_2: string;
}

const props = defineProps<{
  countries: Country[];
}>();

const submit = () => {
    formSubmitted.value = true;

    // Check if image is required but missing
    if (!form.avatar_image) {
        // Scroll to the image upload element
        setTimeout(() => {
            const imageElement = document.getElementById('avatar_image_container');
            if (imageElement) {
                imageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 100);
        return;
    }

    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            formSubmitted.value = false;
        },
    });
};
</script>

<template>
    <AuthBase title="Create Your Account" description="Join our luxury hotel experience today">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-3">
                    <Label for="name" class="text-white flex items-center gap-2">
                        <User class="h-4 w-4 text-[#e0b472]" />
                        Full Name
                    </Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        v-model="form.name"
                        placeholder="John Doe"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.name" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <Label for="email" class="text-white flex items-center gap-2">
                        <Mail class="h-4 w-4 text-[#e0b472]" />
                        Email Address
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.email" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <Label for="password" class="text-white flex items-center gap-2">
                        <Lock class="h-4 w-4 text-[#e0b472]" />
                        Password
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="••••••••"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.password" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <Label for="password_confirmation" class="text-white flex items-center gap-2">
                        <Lock class="h-4 w-4 text-[#e0b472]" />
                        Confirm Password
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="••••••••"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.password_confirmation" class="text-red-300" />
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="grid gap-3">
                        <Label for="gender" class="text-white flex items-center gap-2">
                            <Users class="h-4 w-4 text-[#e0b472]" />
                            Gender
                        </Label>
                        <Select v-model="form.gender" required>
                            <SelectTrigger class="w-full bg-white/10 border-white/20 text-white focus:border-[#e0b472] focus:ring-[#e0b472]/20">
                                <SelectValue placeholder="Select gender" />
                            </SelectTrigger>
                            <SelectContent class="bg-[#0b1626] border-white/20 text-white">
                                <SelectItem value="Male" class="focus:bg-[#e0b472]/20 focus:text-white">Male</SelectItem>
                                <SelectItem value="Female" class="focus:bg-[#e0b472]/20 focus:text-white">Female</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.gender" class="text-red-300" />
                    </div>

                    <div class="grid gap-3">
                        <Label for="country" class="text-white flex items-center gap-2">
                            <MapPin class="h-4 w-4 text-[#e0b472]" />
                            Country
                        </Label>
                        <Select v-model="form.country_id" required>
                            <SelectTrigger class="w-full bg-white/10 border-white/20 text-white focus:border-[#e0b472] focus:ring-[#e0b472]/20">
                                <SelectValue placeholder="Select country" />
                            </SelectTrigger>
                            <SelectContent class="bg-[#0b1626] border-white/20 text-white max-h-[200px] overflow-y-auto">
                                <SelectItem
                                    v-for="country in countries"
                                    :key="country.id"
                                    :value="country.id"
                                    class="focus:bg-[#e0b472]/20 focus:text-white"
                                >
                                    {{ country.name }} ({{ country.iso_alpha_2 }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.country_id" class="text-red-300" />
                    </div>
                </div>

                <div class="grid gap-3">
                    <Label for="mobile" class="text-white flex items-center gap-2">
                        <Phone class="h-4 w-4 text-[#e0b472]" />
                        Mobile Number
                    </Label>
                    <Input
                        id="mobile"
                        type="tel"
                        required
                        v-model="form.mobile"
                        placeholder="+20 123 456 7890"
                        class="bg-white/10 border-white/20 text-white placeholder:text-gray-400 focus:border-[#e0b472] focus:ring-[#e0b472]/20"
                    />
                    <InputError :message="form.errors.mobile" class="text-red-300" />
                </div>

                <div class="grid gap-3">
                    <Label for="avatar_image" class="text-white flex items-center gap-2">
                        <Upload class="h-4 w-4 text-[#e0b472]" />
                        Profile Picture <span class="text-red-400 ml-1">*</span>
                    </Label>
                    <div id="avatar_image_container" class="flex items-center justify-center w-full">
                        <label for="avatar_image"
                            class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-lg cursor-pointer transition-colors overflow-hidden"
                            :class="[
                                imageValidationError
                                    ? 'border-red-500/70 bg-red-500/10'
                                    : 'border-white/20 bg-white/5 hover:bg-white/10'
                            ]"
                        >
                            <!-- Image preview -->
                            <div v-if="imagePreview" class="absolute inset-0 flex items-center justify-center bg-black/20">
                                <img
                                    :src="imagePreview"
                                    alt="Profile Preview"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button
                                        type="button"
                                        @click.prevent="clearImage"
                                        class="p-2 bg-red-500/80 rounded-full text-white hover:bg-red-600 transition-colors"
                                    >
                                        <X class="h-6 w-6" />
                                    </button>
                                </div>
                            </div>

                            <!-- Upload instructions (shown when no image is selected) -->
                            <div v-if="!imagePreview" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div v-if="imageValidationError" class="text-red-400 mb-2">
                                    <AlertCircle class="h-8 w-8" />
                                </div>
                                <div v-else>
                                    <Upload class="w-8 h-8 mb-2 text-gray-300" />
                                </div>
                                <p class="mb-1 text-sm" :class="imageValidationError ? 'text-red-300' : 'text-gray-300'">
                                    {{ imageValidationError ? 'Please select a profile picture' : 'Click to upload' }}
                                </p>
                                <p class="text-xs text-gray-400">PNG, JPG or JPEG</p>
                            </div>

                            <Input
                                id="avatar_image"
                                type="file"
                                accept="image/jpeg,image/png,image/jpg"
                                @input="handleImageSelection($event)"
                                class="hidden"
                            />
                        </label>
                    </div>
                    <div v-if="imageValidationError" class="text-red-300 text-sm flex items-center gap-1.5 mt-1">
                        <AlertCircle class="h-4 w-4" />
                        Profile picture is required
                    </div>
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full bg-[#e0b472] hover:bg-[#d4aa69] text-[#0b1626] font-medium"
                    tabindex="5"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Create Account
                </Button>
            </div>

            <div class="text-center text-sm text-gray-300">
                Already have an account?
                <TextLink
                    :href="route('login')"
                    class="text-[#e0b472] hover:text-[#e0b472]/80"
                    :tabindex="6"
                >
                    Log in
                </TextLink>
            </div>
        </form>
    </AuthBase>
</template>
