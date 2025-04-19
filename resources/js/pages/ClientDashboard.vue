<script setup lang="ts">
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import { getCookie, setCookie, removeCookie } from '@/utils/auth';
import { Calendar, User, Moon, Sun, Monitor, Trash2, LogOut, Clock, CheckCircle2, XCircle } from 'lucide-vue-next';

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
const countries = ref<Country[]>(page.props.countries.data || []);
const activeTab = ref('make-reservation');
const theme = ref(localStorage.getItem('theme') || 'system');
const deleteDialogOpen = ref(false);
const avatarFile = ref<File | null>(null);
const avatarPreview = ref<string | null>(null);
const profileUpdateMessage = ref<{ type: 'success' | 'error', text: string } | null>(null);
const passwordChangeMessage = ref<{ type: 'success' | 'error', text: string } | null>(null);
const avatarError = ref<string | null>(null);

// Profile update form
const profileForm = useForm({
    name: '',
    email: '',
    mobile: '',
    gender: '',
    country_id: ''
});

// Password change form with validation
const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

// Sample reservation data
const reservations = ref([
    { id: 1, date: '2025-04-22', time: '14:00', room: 'Conference Room A', status: 'Confirmed' },
    { id: 2, date: '2025-04-25', time: '10:00', room: 'Meeting Room 3', status: 'Pending' },
    { id: 3, date: '2025-04-30', time: '16:00', room: 'Board Room', status: 'Completed' },
]);

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

onMounted(() => {
    const token = page.props.token;
    if (token) {
        setCookie('client_token', token);
        router.visit('/client/dashboard');
    }

    fetch('/api/client/me', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${getCookie('client_token')}`
        }
    })
        .then(res => res.json())
        .then(data => {
            client.value = data;
            profileForm.defaults({
                name: data.name,
                email: data.email,
                mobile: data.mobile,
                gender: data.gender,
                country_id: data.country_id.toString()
            });
            profileForm.reset();
        })
        .catch(() => {
            router.visit('/login');
        });

    // Apply theme
    applyTheme(theme.value);
});

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
    removeCookie('client_token');
    router.visit('/login');
};

const handleProfileUpdate = () => {
    profileForm.patch('/client/profile/update', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            profileUpdateMessage.value = {
                type: 'success',
                text: 'Profile updated successfully!'
            };
            setTimeout(() => {
                profileUpdateMessage.value = null;
            }, 5000);

            fetch('/api/client/me', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${getCookie('client_token')}`
                }
            })
                .then(res => res.json())
                .then(data => {
                    client.value = data;
                });
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
            // Clear the message after 5 seconds
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
            // Update client data after successful update
            fetch('/api/client/me', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${getCookie('client_token')}`
                }
            })
                .then(res => res.json())
                .then(data => {
                    client.value = data;
                });
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
            handleLogout();
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
                    @click="activeTab = 'make-reservation'">
                    <Calendar class="mr-2 h-4 w-4" />
                    Make Reservation
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'my-reservations' }" @click="activeTab = 'my-reservations'">
                    <Clock class="mr-2 h-4 w-4" />
                    My Reservations
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'profile' }" @click="activeTab = 'profile'">
                    <User class="mr-2 h-4 w-4" />
                    Profile
                </Button>

                <Button variant="ghost" class="w-full justify-start"
                    :class="{ 'bg-secondary': activeTab === 'appearance' }" @click="activeTab = 'appearance'">
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
            <div v-if="activeTab === 'make-reservation'">
                <h1 class="text-2xl font-bold mb-6">Make Reservation</h1>
                <Alert>
                    <Calendar class="h-4 w-4" />
                    <AlertTitle>Coming Soon!</AlertTitle>
                    <AlertDescription>
                        Room reservation feature will be available soon. We're finishing up the final touches!
                    </AlertDescription>
                </Alert>
            </div>

            <!-- My Reservations Tab -->
            <div v-if="activeTab === 'my-reservations'">
                <h1 class="text-2xl font-bold mb-6">My Reservations</h1>
                <Card>
                    <CardContent class="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Time</TableHead>
                                    <TableHead>Room</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="reservation in reservations" :key="reservation.id">
                                    <TableCell>{{ reservation.date }}</TableCell>
                                    <TableCell>{{ reservation.time }}</TableCell>
                                    <TableCell>{{ reservation.room }}</TableCell>
                                    <TableCell>
                                        <span :class="{
                                            'text-green-600': reservation.status === 'Confirmed',
                                            'text-yellow-600': reservation.status === 'Pending',
                                            'text-gray-600': reservation.status === 'Completed'
                                        }">
                                            {{ reservation.status }}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <Button variant="ghost" size="sm">View</Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <!-- Profile Tab -->
            <div v-if="activeTab === 'profile'">
                <h1 class="text-2xl font-bold mb-6">Profile Settings</h1>
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
                                    <AvatarImage :src="avatarPreview || (client ? client.avatar_image : '')"
                                        :alt="client ? client.name : ''" />
                                    <AvatarFallback>{{ client?.name?.charAt(0) }}</AvatarFallback>
                                </Avatar>
                                <div class="space-y-2">
                                    <input type="file" id="avatar" class="hidden" accept="image/*"
                                        @change="handleAvatarChange" />
                                    <Label for="avatar"
                                        class="cursor-pointer inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                                        Choose File
                                    </Label>
                                    <Button v-if="avatarFile" @click="handleAvatarUpdate" :disabled="!avatarFile"
                                        class="ml-2">
                                        Upload Avatar
                                    </Button>
                                    <div v-if="avatarError" class="text-red-500 text-sm">{{ avatarError }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label for="name">Name</Label>
                                    <Input id="name" v-model="profileForm.name" />
                                    <div v-if="profileForm.errors.name" class="text-red-500 text-sm">{{
                                        profileForm.errors.name }}</div>
                                </div>
                                <div class="space-y-2">
                                    <Label for="email">Email</Label>
                                    <Input id="email" type="email" v-model="profileForm.email" />
                                    <div v-if="profileForm.errors.email" class="text-red-500 text-sm">{{
                                        profileForm.errors.email }}</div>
                                </div>
                                <div class="space-y-2">
                                    <Label for="mobile">Mobile</Label>
                                    <Input id="mobile" v-model="profileForm.mobile" />
                                    <div v-if="profileForm.errors.mobile" class="text-red-500 text-sm">{{
                                        profileForm.errors.mobile }}</div>
                                </div>

                                <div class="space-y-2">
                                    <Label for="country">Country</Label>
                                    <Select v-model="profileForm.country_id">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Select country">
                                                {{countries.find(c => c.id.toString() === profileForm.country_id)?.name
                                                || 'Select country' }}
                                            </SelectValue>
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="country in countries" :key="country.id"
                                                :value="country.id.toString()">
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
                                    <div v-if="profileForm.errors.gender" class="text-red-500 text-sm">{{
                                        profileForm.errors.gender }}</div>
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
                            <!-- Message Display -->
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
                                <div v-if="passwordForm.current_password && !isCurrentPasswordFilled"
                                    class="text-red-500 text-sm">
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
                                <div v-if="passwordForm.new_password_confirmation && !doPasswordsMatch"
                                    class="text-red-500 text-sm">
                                    Passwords do not match
                                </div>
                            </div>
                            <Button @click="handlePasswordChange"
                                :disabled="passwordForm.processing || !canSubmitPasswordForm">
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
                                        <Button variant="destructive" @click="handleDeleteAccount">Delete
                                            Account</Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Appearance Tab -->
            <div v-if="activeTab === 'appearance'">
                <h1 class="text-2xl font-bold mb-6">Appearance Settings</h1>
                <Card>
                    <CardHeader>
                        <CardTitle>Theme</CardTitle>
                        <CardDescription>Choose your preferred theme</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-3 gap-4">
                            <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                                :class="{ 'border-primary': theme === 'light' }" @click="applyTheme('light')">
                                <Sun class="h-8 w-8 mb-2" />
                                <span>Light</span>
                            </Button>
                            <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                                :class="{ 'border-primary': theme === 'dark' }" @click="applyTheme('dark')">
                                <Moon class="h-8 w-8 mb-2" />
                                <span>Dark</span>
                            </Button>
                            <Button variant="outline" class="flex flex-col items-center justify-center h-24"
                                :class="{ 'border-primary': theme === 'system' }" @click="applyTheme('system')">
                                <Monitor class="h-8 w-8 mb-2" />
                                <span>System</span>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
