<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Switch } from '@/components/ui/switch'; // adjust to your shadcn-vue path
import { cn } from '@/lib/utils';
import { onMounted, ref, watch } from 'vue';
import { Settings } from 'lucide-vue-next';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { eventBus } from '@/lib/eventBus';

const endpoints = ref([]);

const isDialogOpen = ref(false);

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

watch(isDialogOpen, (newVal) => {
    if (newVal === false) {
        eventBus.emit('reload-charts');
    }
});

onMounted(() => {
    fetchEndpoints();
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <button class="" variant="outline">
                <component :is="Settings" />
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[60%]">
            <DialogHeader>
                <DialogTitle>Dashboard Endpoints Visibility</DialogTitle>
                <DialogDescription>
                    <p>Select endpoints available on dashboard.</p>
                </DialogDescription>
            </DialogHeader>
            <div v-if="endpoints" class="grid items-center gap-4 border-b pb-2 sm:grid-cols-3 xl:grid-cols-4">
                <Card v-for="endpoint in endpoints" :key="endpoint.id" :class="cn('w-auto', $attrs.class ?? '')">
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
            <DialogFooter class="sm:justify-between">
                <DialogClose as-child>
                    <Button type="button" variant="secondary"> Close </Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
