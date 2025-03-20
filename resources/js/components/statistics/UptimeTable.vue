<script setup lang="ts">
import UptimeRow from '@/components/statistics/UptimeRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

const uptimeStats = ref([]);

const fetchUptimeStats = async () => {
    try {
        const response = await fetch(route('api.statistics.uptime'));
        uptimeStats.value = await response.json();
    } catch (error) {
        console.error('Error fetching uptime stats:', error);
    }
};

onMounted(fetchUptimeStats);
</script>

<template>
    <Table class="caption-top border border-gray-200 bg-white">
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
</template>
