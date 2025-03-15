<script setup lang="ts">
    import {
        Table,
        TableBody,
        TableCaption,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
    } from '@/components/ui/table'
    import EndpointRow from "@/components/endpoints/EndpointRow.vue";
    
    defineProps(["endpoints"]);

    const emit = defineEmits(["edit", "delete"]);
    
    const openEditForm = (endpoint) => {
        emit('edit', endpoint);
    }

    const deleteEndpoint = (endpoint) => {
        emit('delete', endpoint);
    }

</script>

<template>
    <Table>
        <TableCaption>A list of your monitored endpoints.</TableCaption>
        <TableHeader>
            <TableRow>
                <TableHead class="w-[100px]">
                    Name
                </TableHead>
                <TableHead>URL</TableHead>
                <TableHead>Method</TableHead>
                <TableHead>Interval</TableHead>
                <TableHead class="text-right">
                    Actions
                </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <EndpointRow v-for="endpoint in endpoints.data" :key="endpoint.id" :endpoint="endpoint" @edit="openEditForm"
                @delete="deleteEndpoint" />
        </TableBody>
    </Table>
</template>