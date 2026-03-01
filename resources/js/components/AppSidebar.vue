<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Folder,
    Info,
    LayoutGrid,
    Book,
    Mail,
    Library,
    MapPin,
} from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { about, contact, courses, dashboard } from '@/routes';
import { index as properties } from '@/routes/properties';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import { index as categories } from '@/routes/categories';
import { index as bookes } from '@/routes/bookes';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Courses',
        href: courses(),
        icon: Book,
    },
    {
        title: 'Properties',
        href: properties(),
        icon: MapPin,
    },
    {
        title: 'Categories',
        href: categories(),
        icon: Library,
    },
    {
        title: 'Books',
        href: bookes(),
        icon: BookOpen,
    },
    {
        title: 'Contact',
        href: contact(),
        icon: Mail,
    },
    {
        title: 'About',
        href: about(),
        icon: Info,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
