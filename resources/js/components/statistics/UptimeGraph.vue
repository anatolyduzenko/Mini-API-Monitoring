<script setup lang="ts">
import { LineChart } from '@/components/ui/chart-line';
import { onMounted, reactive, ref } from 'vue';
import { route } from 'ziggy-js';

const uptimeTrends = ref([]);
const labels = reactive([]);

const fetchUptimeTrend = async () => {
    try {
        const response = await fetch(route('api.statistics.uptimeGraph') + '?days=7');
        const data = await response.json();
        data.labels.forEach((entry) => {
            labels.push(entry);
        });
        data.graphData.forEach((entry) => {
            uptimeTrends.value.push(entry);
        });
    } catch (error) {
        console.error('Error fetching uptime trend:', error);
    }
};

onMounted(() => {
    fetchUptimeTrend();
});
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">API Uptime Trend (Last 7 Days)</h3>
        <LineChart
            v-if="labels.length"
            :data="uptimeTrends"
            :categories="[...labels]"
            index="date"
            :y-formatter="
                (tick, i) => {
                    return typeof tick === 'number' ? `${new Intl.NumberFormat('us').format(tick).toString()}%` : '';
                }
            "
        />
        <p v-else class="text-center text-gray-500">Loading uptime data...</p>
    </div>
</template>
