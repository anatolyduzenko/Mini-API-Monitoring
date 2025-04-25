<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch'; // adjust to your shadcn-vue path
import { cn } from '@/lib/utils';
import { onMounted, ref } from 'vue';

const endpoints = ref([]);

const emit = defineEmits(['close']);

const requestHeaders = new Headers();
requestHeaders.append('Content-Type', 'application/json');
requestHeaders.append('Accept', 'application/json');
requestHeaders.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

const fetchEndpoints = async () => {
    try {
        const response = await fetch(route('api.endpoints.index', { all: 'true' }));
        const data = await response.json();
        endpoints.value = data.map((endpoint) => ({
            ...endpoint,
            dashboard_visible: Boolean(endpoint.dashboard_visible),
        }));
    } catch (error) {
        console.error('Error fetching endpoints:', error);
    }
};

const toggleVisibility = async (endpoint, val) => {
    const res = await fetch(route('api.endpoints.toggleVisibility', [endpoint.id]), {
        method: 'PATCH',
        headers: requestHeaders,
    });

    const data = await res.json();
    endpoint.dashbard_visible = data.dashbard_visible;
};

onMounted(fetchEndpoints);
</script>

<template>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div :class="['w-[60%] rounded bg-background p-6 text-foreground shadow-lg']">
            <h3 class="mb-4 text-lg font-bold">Dashboard Endpoints Visibility</h3>

            <div class="grid items-center gap-4 border-b pb-2 sm:grid-cols-3 xl:grid-cols-4">
                <Card v-if="endpoints" v-for="endpoint in endpoints" :key="endpoint.id" :class="cn('w-auto', $attrs.class ?? '')">
                    <CardContent class="flex items-center justify-between gap-4 p-3">
                        <Label :for="`switch-${endpoint.id}`" class="w-[65%] shrink-0 break-words"
                            ><p class="font-medium">{{ endpoint.name }}</p></Label
                        >
                        <Switch
                            class="shrink-0"
                            :id="`switch-${endpoint.id}`"
                            v-model="endpoint.dashboard_visible"
                            @update:modelValue="(val) => toggleVisibility(endpoint, val)"
                        />
                    </CardContent>
                </Card>
            </div>
            <div class="mt-4 flex justify-between">
                <button @click="emit('close')" class="rounded bg-gray-400 px-4 py-2 text-white">Close</button>
            </div>
        </div>
    </div>
</template>
