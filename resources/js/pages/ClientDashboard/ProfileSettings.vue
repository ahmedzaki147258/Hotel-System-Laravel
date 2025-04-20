<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { getCookie, removeCookie } from '@/utils/auth';
import { Trash2, CheckCircle2, XCircle } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

interface Client {
    id: number;
    name: string;
    email: string;
    avatar_image: string;
    country_id: number;
    country: string;
    gender: string;
    mobile: string;
}

interface Country {
    id: number;
    name: string;
    iso_alpha_2: string;
}

const props = defineProps<{
    client: Client;
    countries: Country[];
}>();

const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const profileUpdateMessage = ref<{ type: 'success' | 'error', text: string } | null>(null);
const passwordChangeMessage = ref<{ type: 'success' | 'error', text: string } | null>(null);
const avatarError = ref<string | null>(null);
const deleteDialogOpen = ref(false);

// Profile update form
const profileForm = useForm({
    name: props.client.name,
    email: props.client.email,
    mobile: props.client.mobile,
    gender: props.client.gender,
    country_id: props.client.country_id.toString()
});

// Password change form with validation
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const isPasswordValid = computed(() => {
    return passwordForm.new_password.length >= 8;
});
const doPasswordsMatch = computed(() => {
    return passwordForm.new_password === passwordForm.new_password_confirmation;
});
const isCurrentPasswordFilled = computed(() => {
    return passwordForm.current_password.length >= 8;
});
const canSubmitPasswordForm = computed(() => {
    return isPasswordValid.value && doPasswordsMatch.value && isCurrentPasswordFilled.value;
});

const emit = defineEmits(['client-updated']);
const handleProfileUpdate = () => {
    profileForm.patch('/client/profile/update', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            profileUpdateMessage.value = {
                type: 'success',
                text: 'Profile updated successfully!'
            };
            emit('client-updated');
            setTimeout(() => {
                profileUpdateMessage.value = null;
            }, 5000);
        },
        onError: (errors) => {
            if (errors.name) {
                profileUpdateMessage.value = {
                    type: 'error',
                    text: errors.name
                };
            }
            setTimeout(() => {
                profileUpdateMessage.value = null;
            }, 5000);
        }
    });
};

const handlePasswordChange = () => {
    if (!canSubmitPasswordForm.value) {
        return;
    }

    passwordForm.patch('/client/password/change', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            passwordForm.reset();
            passwordChangeMessage.value = {
                type: 'success',
                text: 'Password changed successfully!'
            };
            setTimeout(() => {
                passwordChangeMessage.value = null;
            }, 5000);
        },
        onError: (errors) => {
            if (errors.current_password) {
                passwordChangeMessage.value = {
                    type: 'error',
                    text: 'Current password is incorrect'
                };
            } else {
                passwordChangeMessage.value = {
                    type: 'error',
                    text: 'There was an error changing your password. Please try again.'
                };
            }
            setTimeout(() => {
                passwordChangeMessage.value = null;
            }, 5000);
        }
    });
};

const handleAvatarChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        avatarFile.value = target.files[0];

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const handleAvatarUpdate = () => {
    if (!avatarFile.value) return;

    const formData = new FormData();
    formData.append('avatar_image', avatarFile.value);

    router.post('/client/image/update', formData, {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            avatarFile.value = null;
            avatarPreview.value = null;
            emit('client-updated');
        },
        onError: (errors) => {
            if (errors.avatar_image) {
                avatarError.value = errors.avatar_image;
            }
            setTimeout(() => {
                avatarError.value = null;
            }, 5000);
        }
    });
};

const handleDeleteAccount = () => {
    router.delete('/client/profile/delete', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteDialogOpen.value = false;
            removeCookie('client_token');
        }
    });
};
</script>

<template>
    <div class="space-y-6">
        <Card>
            <CardHeader>
                <CardTitle>Personal Information</CardTitle>
                <CardDescription>Update your profile information</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div v-if="profileUpdateMessage" class="flex items-center p-4 rounded-md" :class="{
                    'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400': profileUpdateMessage.type === 'success',
                    'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400': profileUpdateMessage.type === 'error'
                }">
                    <component :is="profileUpdateMessage.type === 'success' ? CheckCircle2 : XCircle"
                        class="h-5 w-5 mr-2" />
                    {{ profileUpdateMessage.text }}
                </div>

                <div class="flex items-center space-x-4">
                    <Avatar class="w-24 h-24">
                        <AvatarImage :src="avatarPreview || client.avatar_image" :alt="client.name" />
                        <AvatarFallback>{{ client.name.charAt(0) }}</AvatarFallback>
                    </Avatar>
                    <div class="space-y-2">
                        <input type="file" id="avatar" class="hidden" accept="image/*" @change="handleAvatarChange" />
                        <Label for="avatar"
                            class="cursor-pointer inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                            Choose File
                        </Label>
                        <Button v-if="avatarFile" @click="handleAvatarUpdate" :disabled="!avatarFile" class="ml-2">
                            Upload Avatar
                        </Button>
                        <div v-if="avatarError" class="text-red-500 text-sm">{{ avatarError }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="profileForm.name" />
                        <div v-if="profileForm.errors.name" class="text-red-500 text-sm">{{ profileForm.errors.name }}</div>
                    </div>
                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" type="email" v-model="profileForm.email" />
                        <div v-if="profileForm.errors.email" class="text-red-500 text-sm">{{ profileForm.errors.email }}</div>
                    </div>
                    <div class="space-y-2">
                        <Label for="mobile">Mobile</Label>
                        <Input id="mobile" v-model="profileForm.mobile" />
                        <div v-if="profileForm.errors.mobile" class="text-red-500 text-sm">{{ profileForm.errors.mobile }}</div>
                    </div>

                    <div class="space-y-2">
                        <Label for="country">Country</Label>
                        <Select v-model="profileForm.country_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Select country">
                                    {{ countries.find(c => c.id.toString() === profileForm.country_id)?.name || 'Select country' }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="country in countries" :key="country.id" :value="country.id.toString()">
                                    {{ country.name }} ({{ country.iso_alpha_2 }})
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <div v-if="profileForm.errors.country_id" class="text-red-500 text-sm">
                            {{ profileForm.errors.country_id }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="gender">Gender</Label>
                        <select id="gender" v-model="profileForm.gender"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <div v-if="profileForm.errors.gender" class="text-red-500 text-sm">{{ profileForm.errors.gender }}</div>
                    </div>
                </div>

                <Button @click="handleProfileUpdate" :disabled="profileForm.processing">
                    {{ profileForm.processing ? 'Updating...' : 'Update Profile' }}
                </Button>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Change Password</CardTitle>
                <CardDescription>Update your password</CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div v-if="passwordChangeMessage" class="flex items-center p-4 rounded-md" :class="{
                    'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400': passwordChangeMessage.type === 'success',
                    'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400': passwordChangeMessage.type === 'error'
                }">
                    <component :is="passwordChangeMessage.type === 'success' ? CheckCircle2 : XCircle"
                        class="h-5 w-5 mr-2" />
                    {{ passwordChangeMessage.text }}
                </div>

                <div class="space-y-2">
                    <Label for="current_password">Current Password</Label>
                    <Input id="current_password" type="password" v-model="passwordForm.current_password"
                        :class="{ 'border-red-500': passwordForm.errors.current_password }" />
                    <div v-if="passwordForm.errors.current_password" class="text-red-500 text-sm">
                        {{ passwordForm.errors.current_password }}
                    </div>
                    <div v-if="passwordForm.current_password && !isCurrentPasswordFilled" class="text-red-500 text-sm">
                        Password must be at least 8 characters long
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="new_password">New Password</Label>
                    <Input id="new_password" type="password" v-model="passwordForm.new_password"
                        :class="{ 'border-red-500': passwordForm.errors.new_password }" />
                    <div v-if="passwordForm.errors.new_password" class="text-red-500 text-sm">
                        {{ passwordForm.errors.new_password }}
                    </div>
                    <div v-if="passwordForm.new_password && !isPasswordValid" class="text-red-500 text-sm">
                        Password must be at least 8 characters long
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="new_password_confirmation">Confirm Password</Label>
                    <Input id="new_password_confirmation" type="password"
                        v-model="passwordForm.new_password_confirmation"
                        :class="{ 'border-red-500': passwordForm.errors.new_password_confirmation }" />
                    <div v-if="passwordForm.errors.new_password_confirmation" class="text-red-500 text-sm">
                        {{ passwordForm.errors.new_password_confirmation }}
                    </div>
                    <div v-if="passwordForm.new_password_confirmation && !doPasswordsMatch" class="text-red-500 text-sm">
                        Passwords do not match
                    </div>
                </div>
                <Button @click="handlePasswordChange" :disabled="passwordForm.processing || !canSubmitPasswordForm">
                    {{ passwordForm.processing ? 'Changing Password...' : 'Change Password' }}
                </Button>
            </CardContent>
        </Card>

        <Card class="border-red-200">
            <CardHeader>
                <CardTitle class="text-red-600">Danger Zone</CardTitle>
                <CardDescription>Permanently delete your account</CardDescription>
            </CardHeader>
            <CardContent>
                <Dialog v-model:open="deleteDialogOpen">
                    <DialogTrigger asChild>
                        <Button variant="destructive">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete Account
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Are you absolutely sure?</DialogTitle>
                            <DialogDescription>
                                This action cannot be undone. This will permanently delete your account and
                                remove your data from our servers.
                            </DialogDescription>
                        </DialogHeader>
                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" @click="deleteDialogOpen = false">Cancel</Button>
                            <Button variant="destructive" @click="handleDeleteAccount">Delete Account</Button>
                        </div>
                    </DialogContent>
                </Dialog>
            </CardContent>
        </Card>
    </div>
</template>
