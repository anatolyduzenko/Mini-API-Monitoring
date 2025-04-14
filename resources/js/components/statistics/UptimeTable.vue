<script setup lang="ts">
import DetailedChart from '@/components/statistics/DetailedChart.vue';
import UptimeRow from '@/components/statistics/UptimeRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { EndpointStatRecord } from '@/types/app/endpointstatrecord';
import { onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';

import { Pagination, PaginationFirst, PaginationLast, PaginationNext, PaginationPrev } from '@/components/ui/pagination';

const uptimeStats = ref<EndpointStatRecord[]>([]);
const currentPage = ref(1);
const totalItems = ref(1);
const selectedEndpoint = ref<EndpointStatRecord>();
const showDialog = ref(false);

const fetchUptimeStats = async (page = 1) => {
    try {
        const response = await fetch(route('api.statistics.uptime', { page: page }));
        const data = await response.json();
        uptimeStats.value = data.data;
        currentPage.value = data.current_page;
        totalItems.value = data.total;
    } catch (error) {
        console.error('Error fetching uptime stats:', error);
    }
};

const openDialog = async (endpoint: EndpointStatRecord) => {
    selectedEndpoint.value = endpoint;
    showDialog.value = true;
};

watch(currentPage, (page) => {
    fetchUptimeStats(page);
});

onMounted(fetchUptimeStats);
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">API Uptime Stats</h3>
        <Table class="caption-top border">
            <TableCaption class="mt-0 pb-4">Double click to open Detailed chart.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-left">Endpoint Name</TableHead>
                    <TableHead class="text-center">Uptime (%)</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <UptimeRow
                    v-for="endpoint in uptimeStats"
                    :key="endpoint.id"
                    :endpoint="endpoint"
                    @dblclick="openDialog(endpoint)"
                    class="border text-center"
                />
            </TableBody>
        </Table>
        <div v-if="totalItems > 7" class="p-4">
            <Pagination v-model:page="currentPage" :items-per-page="7" :total="totalItems" :sibling-count="1" show-edges :default-page="2">
                <PaginationFirst />
                <PaginationPrev />
                <PaginationNext />
                <PaginationLast />
            </Pagination>
        </div>
        <DetailedChart v-if="selectedEndpoint" v-model:open="showDialog" :endpoint="selectedEndpoint" />
    </div>
</template>
