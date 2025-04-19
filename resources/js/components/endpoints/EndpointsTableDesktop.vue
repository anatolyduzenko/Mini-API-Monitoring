<script setup lang="ts">
import EndpointRow from '@/components/endpoints/EndpointRow.vue';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { KeyRound, SquareActivity } from 'lucide-vue-next';

defineProps(['endpoints']);

const emit = defineEmits(['edit', 'delete']);

const openEditForm = (endpoint) => {
    emit('edit', endpoint);
};

const deleteEndpoint = (endpoint) => {
    emit('delete', endpoint);
};
</script>

<template>
    <div class="rounded-md border">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Name</TableHead>
                    <TableHead>URL</TableHead>
                    <TableHead>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <component :is="SquareActivity" />
                                </TooltipTrigger>
                                <TooltipContent> Show on Dashboard </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </TableHead>
                    <TableHead>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger as-child>
                                    <component :is="KeyRound" />
                                </TooltipTrigger>
                                <TooltipContent> Authentication type </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </TableHead>
                    <TableHead>Method</TableHead>
                    <TableHead>Interval(min)</TableHead>
                    <TableHead>Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <EndpointRow v-for="endpoint in endpoints" :key="endpoint.id" :endpoint="endpoint" @edit="openEditForm" @delete="deleteEndpoint" />
            </TableBody>
        </Table>
    </div>
</template>
