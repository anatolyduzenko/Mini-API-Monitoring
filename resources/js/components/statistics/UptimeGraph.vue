<script setup lang="ts">
import AutoRefreshSwitch from '@/components/statistics/AutoRefreshSwitch.vue';
import ChartSettings from '@/components/statistics/ChartSettings.vue';
import { LineChart } from '@/components/ui/chart-line';
import { useChartColors } from '@/composables/useChartColors';
import { onMounted, onUnmounted, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { eventBus } from '@/lib/eventBus';

const uptimeTrends = ref<Record<string, any>[]>([]);
const labels = reactive<string[]>([]);
const colors = ref<string[]>([]);
const { chartColors, getRandomHSLColors } = useChartColors();
const autoRefreshEnabled = ref(false);
const splitType = ref('hourly');
const chartKey = ref(0);

let intervalId: ReturnType<typeof setInterval>;

const reloadChart = () => {
    chartKey.value++;
    fetchUptimeTrend();
};

const fetchUptimeTrend = async (splitType = 'hourly') => {
    try {
        uptimeTrends.value.splice(0);
        labels.splice(0);
        const response = await fetch(route('api.statistics.uptimeGraph') + '?days=7&split_type=' + splitType);
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

watch(splitType, (val) => {
    clearInterval(intervalId);
    fetchUptimeTrend(val);
    intervalId = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchUptimeTrend(val);
        }
    }, 30_000);
});

onMounted(() => {
    fetchUptimeTrend();
    intervalId = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchUptimeTrend();
        }
    }, 30_000);
    eventBus.on('reload-charts', reloadChart);
});

onUnmounted(() => {
    clearInterval(intervalId);
    eventBus.off('reload-charts', reloadChart);
});
</script>

<template>
    <div class="p-5">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="text-md font-bold">API Uptime Trend (Last 7 Days)</h3>
            <AutoRefreshSwitch v-model:enabled="autoRefreshEnabled" label="Auto-refresh" />
            <ChartSettings
                v-model="splitType"
                :options="[
                    { label: 'Hour', value: 'hourly' },
                    { label: '10 Min', value: 'decamin' },
                ]"
            />
        </div>
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
