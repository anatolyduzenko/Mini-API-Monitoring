<script setup lang="ts">
import UptimeRow from '@/components/statistics/UptimeRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { TailwindPagination } from 'laravel-vue-pagination';
import { onMounted, ref, nextTick } from 'vue';
import { route } from 'ziggy-js';
import DetailedChart from '@/components/statistics/DetailedChart.vue';
import type { EndpointStatRecord } from '@/types/app/endpointstatrecord';

const uptimeStats = ref<EndpointStatRecord[]>([]);
const responseData = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const selectedEndpoint = ref<EndpointStatRecord>();
const showDialog = ref(false);

const fetchUptimeStats = async (page = 1) => {
    try {
        const response = await fetch(route('api.statistics.uptime', { page: page }));
        const data = await response.json();
        responseData.value = data;
        uptimeStats.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
    } catch (error) {
        console.error('Error fetching uptime stats:', error);
    }
};

const openDialog = async (endpoint) => {
  selectedEndpoint.value = endpoint
  showDialog.value = true
}

onMounted(fetchUptimeStats);
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">API Uptime Stats</h3>
        <Table class="caption-top border">
            <TableCaption class="pb-4 mt-0">Double click to open Detailed chart.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-left">Endpoint Name</TableHead>
                    <TableHead class="text-center">Uptime (%)</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <UptimeRow v-for="endpoint in uptimeStats" :key="endpoint.id" :endpoint="endpoint" @dblclick="openDialog(endpoint)" class="border text-center" />
            </TableBody>
        </Table>
        <!-- <TailwindPagination class="pt-4" :data="responseData" @pagination-change-page="fetchUptimeStats" /> -->
        <DetailedChart v-if="selectedEndpoint"
            v-model:open="showDialog"
            :endpoint="selectedEndpoint" />
    </div>
</template>
