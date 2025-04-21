<script setup lang="ts">
import AutoRefreshSwitch from '@/components/AutoRefreshSwitch.vue';
import { LineChart } from '@/components/ui/chart-line';
import { useChartColors } from '@/composables/useChartColors';
import { onMounted, onUnmounted, reactive, ref } from 'vue';
import { route } from 'ziggy-js';

const uptimeTrends = ref<Record<string, any>[]>([]);
const labels = reactive<string[]>([]);
const colors = ref<string[]>([]);
const { chartColors, getRandomHSLColors } = useChartColors();
const autoRefreshEnabled = ref(true);

let intervalId: ReturnType<typeof setInterval>;

const fetchUptimeTrend = async () => {
    try {
        uptimeTrends.value.splice(0);
        const response = await fetch(route('api.statistics.uptimeGraph') + '?days=7&split_type=hourly');
        const data = await response.json();
        data.labels.forEach((entry: string) => {
            labels.push(entry);
        });
        data.graphData.forEach((entry) => {
            uptimeTrends.value.push(entry);
        });

        if (chartColors.value === 'random') {
            colors.value = getRandomHSLColors(labels.length);
        }
    } catch (error) {
        console.error('Error fetching uptime trend:', error);
    }
};

onMounted(() => {
    fetchUptimeTrend();
    intervalId = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchUptimeTrend();
        }
    }, 30_000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">API Uptime Trend (Last 7 Days)</h3>
        <AutoRefreshSwitch v-model:enabled="autoRefreshEnabled" label="Auto-refresh" />
        <LineChart
            v-if="labels.length"
            :data="uptimeTrends"
            :categories="[...labels]"
            index="date"
            :colors="colors"
            :y-formatter="
                (tick, i) => {
                    return typeof tick === 'number' ? `${new Intl.NumberFormat('us').format(tick).toString()}%` : '';
                }
            "
        />
        <p v-else class="text-center text-gray-500">Loading uptime data...</p>
    </div>
</template>
