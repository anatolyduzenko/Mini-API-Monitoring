<script setup lang="ts">
import { AreaChart } from '@/components/ui/chart-area';
import { useChartColors } from '@/composables/useChartColors';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';
const { getRandomHSLColors } = useChartColors();

const responseTime = ref([]);
const labels = ref([]);
const colors = ref<string[]>([]);

const fetchResponseTime = async () => {
    try {
        const response = await fetch(route('api.statistics.responseTime') + '?days=31');
        const data = await response.json();
        data.labels.forEach((entry) => {
            labels.value.push(entry);
        });
        data.graphData.forEach((entry) => {
            responseTime.value.push(entry);
        });
        colors.value = getRandomHSLColors(labels.value.length);
    } catch (error) {
        console.error('Error fetching uptime trend:', error);
    }
};

onMounted(() => {
    fetchResponseTime();
});
</script>

<template>
    <div class="p-5">
        <h3 class="text-md mb-4 font-bold">API Response Time (Average)</h3>
        <AreaChart v-if="labels.length" :data="responseTime" :categories="labels" index="date" :colors="colors" />
    </div>
</template>
