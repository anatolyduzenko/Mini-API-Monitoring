<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { ref, nextTick, watch } from "vue";
import { route } from "ziggy-js";
import { LineChart } from '@/components/ui/chart-line'
import { EndpointStatRecord } from '@/types/app/endpointstatrecord';

const props = defineProps<{
    endpoint: EndpointStatRecord
}>();

const uptimeTrends = ref([]);
const labels = ref([]);
const isOpen = defineModel<boolean>('open', { default: false });

const fetchUptimeTrend = async (endpoint) => {
    try {
        labels.value = [];
        uptimeTrends.value = [];
        const response = await fetch(route("api.statistics.uptimeGraph") + "?endpoint_id=" + endpoint + "&days=365");
        const data = await response.json();
        data.labels.forEach(entry => {
            labels.value.push(entry);
        });
        data.graphData.forEach(entry => {
            uptimeTrends.value.push(entry);
        });
    } catch (error) {
        console.error("Error fetching uptime trend:", error);
    }
};

watch (
  () => props.endpoint,
  (newVal) => {
    if (newVal?.id) {
      fetchUptimeTrend(newVal.id)
    }
  },
  { immediate: true }
)

</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-[75%]">
      <DialogHeader>
        <DialogTitle>Detailed View</DialogTitle>
        <DialogDescription>
            Uptime chart for {{ endpoint.name }}
        </DialogDescription>
      </DialogHeader>
      <div class="grid gap-4 py-4">
        <LineChart v-if="endpoint?.id && labels.length"
            :data="uptimeTrends"
            :categories="[...labels]"
            index="date"
            :y-formatter="(tick, i) => {
            return typeof tick === 'number'
                ? `${new Intl.NumberFormat('us').format(tick).toString()}%`
                : ''
            }"
        />
      </div>
      <DialogFooter>
        <Button @click="isOpen = false">
          Close
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>