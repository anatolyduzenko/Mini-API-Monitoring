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
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { ref, onMounted, reactive } from "vue";
import { route } from "ziggy-js";
import { LineChart } from '@/components/ui/chart-line'
import EndpointForm from '../endpoints/EndpointForm.vue';

const props = defineProps(['endpoint']);

const uptimeTrends = ref([]);
const labels = ref([]);

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

</script>

<template>
  <Dialog @update:open="fetchUptimeTrend(endpoint.id)">
    <DialogTrigger as-child>
        <Button variant="outline">
        Detailed
        </Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[75%]">
      <DialogHeader>
        <DialogTitle>Detailed View</DialogTitle>
        <DialogDescription>
          -----Description goes here
        </DialogDescription>
      </DialogHeader>
      <div class="grid gap-4 py-4">
        <LineChart v-if="labels.length"
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
        <Button type="submit">
          Close
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>