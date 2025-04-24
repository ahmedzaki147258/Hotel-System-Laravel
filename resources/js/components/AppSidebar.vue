<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Bed, Building, CalendarCheck, CheckCircle, ConciergeBell, Folder, LayoutGrid, ShieldCheck, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const userRole = computed(() => {
    const roles = usePage().props.auth.user.roles;
    return roles && roles.length > 0 ? roles[0].name : 'Client';
});

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
        roles: ['admin', 'manager', 'receptionist'], // Accessible to all roles
    },
    {
        title: 'Manage Managers',
        href: '/managers',
        icon: ShieldCheck,
        roles: ['admin'], // Only admin can access
    },
    {
        title: 'Manage Receptionists',
        href: '/receptionists',
        icon: ConciergeBell,
        roles: ['admin', 'manager'],
    },
    {
        title: 'Manage Clients',
        href: '/clients',
        icon: Users,
        roles: ['admin', 'manager', 'receptionist'],
    },
    {
        title: 'My Approved Clients',
        href: '/my-approved-clients',
        icon: CheckCircle,
        roles: ['admin', 'manager', 'receptionist'],
    },
    {
        title: 'Clients Reservations',
        href: '/reservations',
        icon: CalendarCheck,
        roles: ['admin', 'manager', 'receptionist'], // Manager and Receptionist can access
    },
    {
        title: 'Manage Floors',
        href: '/floors',
        icon: Building,
        roles: ['admin', 'manager'],
    },
    {
        title: 'Manage Rooms',
        href: '/rooms',
        icon: Bed,
        roles: ['admin', 'manager'],
    },
];

// Filter sidebar items based on the current user's role
const filteredNavItems = computed(() => {
    return mainNavItems.filter((item) => item.roles.includes(userRole.value));
});

// Footer items remain the same for all users
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/ahmedzaki147258/Hotel-System-Laravel',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- Sidebar Header with the logo and link to the dashboard -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Sidebar Content with dynamic navigation items based on the user role -->
        <SidebarContent>
            <!-- Render sidebar items based on filteredNavItems (role-based) -->
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <!-- Sidebar Footer with static footer items -->
        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
