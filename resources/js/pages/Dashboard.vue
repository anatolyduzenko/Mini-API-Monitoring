<script setup lang="ts">
import RecentTable from '@/components/statistics/RecentTable.vue';
import UptimeTable from '@/components/statistics/UptimeTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <UptimeTable />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <RecentTable />
                </div>
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border"></div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
