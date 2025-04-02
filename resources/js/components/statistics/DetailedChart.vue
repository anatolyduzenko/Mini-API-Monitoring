<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AreaChart from '@/components/ui/chart-area/AreaChart.vue';
import { BarChart } from '@/components/ui/chart-bar';
import { LineChart } from '@/components/ui/chart-line';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Progress } from '@/components/ui/progress';
import { useChartColors } from '@/composables/useChartColors';
import { EndpointStatRecord } from '@/types/app/endpointstatrecord';
import { onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
const { getRandomHSLColors } = useChartColors();

const props = withDefaults(
    defineProps<{
        endpoint: EndpointStatRecord;
        reportDays?: number;
        splitType?: string;
    }>(),
    {
        reportDays: 365,
        splitType: 'daily',
    },
);

const uptimeTrends = ref<Record<string, any>[]>([]);
const labels = ref<string[]>([]);
const isOpen = defineModel<boolean>('open', { default: false });
const reportRanges = ref<Record<string, any>>({});
const localReportDays = ref(props.reportDays);
const localSplitType = ref(props.splitType);
const progress = ref(10);
const loading = ref(false);
const chartType = ref('line');
const chartTypes = [
    {
        id: 'area',
        name: 'Area',
    },
    {
        id: 'bar',
        name: 'Bar',
    },
    {
        id: 'line',
        name: 'Line',
    },
];
const splitTypes = ref<Record<string, any>>({});
const colors = ref<string[]>([]);

const fetchReportRanges = async () => {
    try {
        const response = await fetch(route('api.reportRanges.index'));
        const data = await response.json();
        reportRanges.value = data;
    } catch (error) {
        console.error('Error fetcing Report Ranges:', error);
    }
};

const fetchSplitTypes = async () => {
    try {
        const response = await fetch(route('api.splitTypes.index'));
        const data = await response.json();
        splitTypes.value = data;
    } catch (error) {
        console.error('Error fetching Split Types:', error);
    }
};

const fetchUptimeTrend = async (endpoint: number, days = 365, split = 'daily') => {
    try {
        loading.value = true;
        progress.value = 10;
        const timer = setTimeout(() => (progress.value = 78), 50);
        labels.value = [];
        uptimeTrends.value = [];
        const response = await fetch(route('api.statistics.uptimeGraph') + '?endpoint_id=' + endpoint + `&days=${days}&split_type=${split}`);
        const data = await response.json();
        data.labels.forEach((entry: string) => {
            labels.value.push(entry);
        });
        data.graphData.forEach((entry) => {
            uptimeTrends.value.push(entry);
        });
        colors.value = getRandomHSLColors(labels.value.length);
        progress.value = 100;
        clearTimeout(timer);
        loading.value = false;
    } catch (error) {
        console.error('Error fetching uptime trend:', error);
    }
};

watch(
    () => [props.endpoint, localReportDays.value, localSplitType.value],
    ([newEndpoint, newDays, newSplitType], [_, oldDays, oldSplitType] = []) => {
        if (newEndpoint?.id || newDays !== oldDays || newSplitType !== oldSplitType) {
            fetchUptimeTrend(newEndpoint.id, newDays, newSplitType);
        }
    },
    { immediate: true },
);

onMounted(() => {
    fetchReportRanges();
    fetchSplitTypes();
});
</script>

<template>
    <Dialog v-model:open="isOpen">
        <DialogContent class="sm:max-w-[75%]">
            <DialogHeader>
                <DialogTitle>Detailed Uptime Chart for {{ endpoint.name }}</DialogTitle>
                <DialogDescription> Showing uptime data for the selected period. </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-2">
                <div class="items-left flex flex-col justify-between space-y-2 py-2 sm:flex-row sm:space-x-2 sm:space-y-0">
                    <div class="sm:space-x-2">
                        <Button
                            v-for="range in reportRanges"
                            :key="range.id"
                            @click="localReportDays = range.id"
                            :variant="localReportDays === range.id ? 'default' : 'outline'"
                        >
                            {{ range.name }}
                        </Button>
                    </div>
                    <div class="flex flex-row sm:space-x-2">
                        <!-- <div v-if="localReportDays === 1 || localReportDays === 3" class="sm:space-x-2"> -->
                        <Button
                            v-for="type in splitTypes"
                            :key="type.id"
                            @click="localSplitType = type.id"
                            :variant="localSplitType === type.id ? 'default' : 'outline'"
                        >
                            {{ type.name }}
                        </Button>
                        <!-- </div> -->
                        <!-- <div class="sm:space-x-2"> -->
                        <Button
                            v-for="type in chartTypes"
                            :key="type.id"
                            @click="chartType = type.id"
                            :variant="chartType === type.id ? 'default' : 'outline'"
                        >
                            {{ type.name }}
                        </Button>
                        <!-- </div> -->
                    </div>
                </div>
                <div v-if="endpoint?.id && labels.length">
                    <LineChart
                        v-if="endpoint?.id && labels.length && chartType == 'line'"
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
                    <AreaChart
                        v-else-if="endpoint?.id && labels.length && chartType == 'area'"
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
                    <BarChart
                        v-else-if="endpoint?.id && labels.length && chartType == 'bar'"
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
                </div>
                <div v-else-if="loading" class="flex flex-col items-center justify-center space-y-2 py-2 sm:flex-row sm:space-x-2 sm:space-y-0">
                    <Progress v-model="progress" class="w-3/5" />
                </div>
                <div v-else class="flex flex-col items-center justify-center space-y-2 py-2 sm:flex-row sm:space-x-2 sm:space-y-0">
                    <p>No Data Available.</p>
                </div>
            </div>
            <DialogFooter>
                <Button @click="isOpen = false"> Close </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
