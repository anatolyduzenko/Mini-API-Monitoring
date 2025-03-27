<script setup lang="ts">
import LogsTable from '@/components/logs/LogsTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Endpoints logs',
        href: '/logs',
    },
];
</script>

<template>
    <Head title="Endpoints Logs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <LogsTable />
    </AppLayout>
</template>
