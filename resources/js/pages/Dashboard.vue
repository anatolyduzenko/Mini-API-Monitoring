<script setup lang="ts">
import AreaGraph from '@/components/statistics/AreaGraph.vue';
import RecentTable from '@/components/statistics/RecentTable.vue';
import UptimeGraph from '@/components/statistics/UptimeGraph.vue';
import UptimeTable from '@/components/statistics/UptimeTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

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
            <div class="grid auto-rows-min grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-2">
                <div class="overflow-show relative aspect-video rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <UptimeGraph />
                </div>
                <div class="overflow-show relative aspect-video rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <AreaGraph />
                </div>
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-2">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <UptimeTable />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 px-4 dark:border-sidebar-border">
                    <RecentTable />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
