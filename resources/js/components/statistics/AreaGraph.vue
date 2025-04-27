<script setup lang="ts">
import AutoRefreshSwitch from '@/components/statistics/AutoRefreshSwitch.vue';
import ChartSettings from '@/components/statistics/ChartSettings.vue';

import { AreaChart } from '@/components/ui/chart-area';
import { useChartColors } from '@/composables/useChartColors';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
const { chartColors, getRandomHSLColors } = useChartColors();

const responseTime = ref<Record<string, any>[]>([]);
const labels = ref<string[]>([]);
const colors = ref<string[]>([]);
const autoRefreshEnabled = ref(false);

const splitType = ref('decamin');

let intervalId: ReturnType<typeof setInterval>;

const fetchResponseTime = async (splitType = 'decamin') => {
    try {
        responseTime.value.splice(0);
        const response = await fetch(route('api.statistics.responseTime') + '?days=1&split_type=' + splitType);
        const data = await response.json();
        data.labels.forEach((entry: string) => {
            labels.value.push(entry);
        });
        data.graphData.forEach((entry) => {
            responseTime.value.push(entry);
        });
        if (chartColors.value === 'random') {
            colors.value = getRandomHSLColors(labels.value.length);
        }
    } catch (error) {
        console.error('Error fetching uptime trend:', error);
    }
};

watch(splitType, (val) => {
    clearInterval(intervalId);
    fetchResponseTime(val);
    intervalId = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchResponseTime(val);
        }
    }, 30_000);
});

onMounted(() => {
    fetchResponseTime();
    intervalId = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchResponseTime();
        }
    }, 30_000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});
</script>

<template>
    <div class="p-5">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="text-md font-bold">API Response Time (Average)</h3>
            <AutoRefreshSwitch v-model:enabled="autoRefreshEnabled" label="Auto-refresh" />
            <ChartSettings
                v-model="splitType"
                :options="[
                    { label: 'Hour', value: 'hourly' },
                    { label: '10 Min', value: 'decamin' },
                ]"
            />
        </div>
        <AreaChart v-if="labels.length" :data="responseTime" :categories="labels" index="date" :colors="colors" />
    </div>
</template>
