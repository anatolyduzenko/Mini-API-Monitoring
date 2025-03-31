<script setup lang="ts">
import RecentRow from '@/components/statistics/RecentRow.vue';
import { Table, TableBody, TableCaption, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

const recentLogs = ref([]);

const fetchRecentLogs = async () => {
    try {
        const response = await fetch(route('api.statistics.recent'));
        recentLogs.value = await response.json();
    } catch (error) {
        console.error('Error fetching recent logs:', error);
    }
};

onMounted(fetchRecentLogs);
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">Most Recent Logs</h3>
        <Table class="caption-top border">
            <TableCaption class="pb-4 pt-0">Most recent API logs.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="text-left">Endpoint Name</TableHead>
                    <TableHead class="text-center">Status Code</TableHead>
                    <TableHead class="text-center">Response Time(ms)</TableHead>
                    <TableHead class="text-center">Checked At</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <RecentRow v-for="endpoint in recentLogs" :key="endpoint.name" :endpoint="endpoint" class="border text-center" />
            </TableBody>
        </Table>
    </div>
</template>
