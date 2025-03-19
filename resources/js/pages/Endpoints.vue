<script setup lang="ts">
import EndpointsTable from '@/components/endpoints/EndpointsTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Endpoints management',
        href: '/endpoints',
    },
];
</script>

<template>
    <Head title="API Endpoints" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <EndpointsTable />
    </AppLayout>
</template>
