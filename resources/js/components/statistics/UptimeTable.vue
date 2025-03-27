<script setup lang="ts">
import UptimeRow from '@/components/statistics/UptimeRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { TailwindPagination } from 'laravel-vue-pagination';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

const uptimeStats = ref([]);
const responseData = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);

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

onMounted(fetchUptimeStats);
</script>

<template>
    <div>
        <Table class="caption-top border">
            <TableCaption>API Uptime Statistics.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-center">Endpoint Name</TableHead>
                    <TableHead class="text-center">Uptime (%)</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <UptimeRow v-for="endpoint in uptimeStats" :key="endpoint.name" :endpoint="endpoint" class="border text-center" />
            </TableBody>
        </Table>
        <TailwindPagination class="pt-4" :data="responseData" @pagination-change-page="fetchUptimeStats" />
    </div>
</template>
