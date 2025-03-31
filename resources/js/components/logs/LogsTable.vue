<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Endpoint } from '@/types/app/endpoint';
import type { LogRecord } from '@/types/app/logrecord';
import { onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { columns } from './columns';

import LogsDataTable from '@/components/logs/LogsDataTable.vue';

const logs = ref<LogRecord[]>([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(15);
const endpointId = ref(null);
const endpoints = ref<Endpoint[]>([]);
const statusCode = ref(null);
const statusCodes = ref({});

const fetchLogs = async (page = 1, newPerPage = 15, endpointId = null, statusCode = null) => {
    try {
        loading.value = true;
        const response = await fetch(
            route('api.logs.index', {
                page: page,
                per_page: newPerPage,
                endpoint_id: endpointId,
                status_code: statusCode,
            }),
        );
        const data = await response.json();
        logs.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
        perPage.value = data.per_page;
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
};

const fetchEndpoints = async () => {
    try {
        const page = 1;
        let hasMore = true;
        while (hasMore) {
            const response = await fetch(route('api.endpoints.index') + `?page=${page}`);
            const data = await response.json();
            hasMore = page < data.last_page;
            Object.assign(endpoints.value, data.data);
        }
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
};

const fetchStatusCodes = async () => {
    try {
        const response = await fetch(route('api.statusCodes.index'));
        const data = await response.json();
        statusCodes.value = data;
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
};

watch(
    [currentPage, perPage, endpointId, statusCode],
    ([newPage, newPerPage, newEndpointId, newStatusCode], [_, oldPerPage, oldEndpointId, oldStatusCode]) => {
        if (newPerPage !== oldPerPage || newEndpointId != oldEndpointId || newStatusCode != oldStatusCode) {
            newPage = 1;
        }
        fetchLogs(newPage, newPerPage, newEndpointId, newStatusCode);
    },
);

onMounted(() => {
    fetchEndpoints();
    fetchStatusCodes();
    fetchLogs();
});
</script>

<template>
    <div class="">
        <div class="p-4">
            <div class="flex flex-col items-center justify-between space-y-2 py-4 sm:flex-row sm:space-x-2 sm:space-y-0">
                <div class="flex-column flex items-center space-x-2 py-2">
                    <span>Endpoint Name:</span>
                    <Select v-model="endpointId" :defaultValue="endpointId" class="h-[37px]">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Select Endpoint" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="endpoint in endpoints" :key="endpoint.id" :value="endpoint.id"> {{ endpoint.name }} </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <span>Status Code:</span>
                    <Select v-model="statusCode" :defaultValue="statusCode" class="h-[37px]">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Select Status Code" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="code in statusCodes" :key="code.id" :value="code.id"> {{ code.id }} | {{ code.name }} </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex-column flex items-center space-x-2 py-2">
                    <span>Show:</span>
                    <Select v-model="perPage" :defaultValue="perPage" class="h-[37px]">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="'Per page" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectLabel>Per Page</SelectLabel>
                                <SelectItem :value="15"> 15 </SelectItem>
                                <SelectItem :value="25"> 25 </SelectItem>
                                <SelectItem :value="50"> 50 </SelectItem>
                                <SelectItem :value="100"> 100 </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                    <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--"> Previous </Button>
                    <span class="px-4"> Page {{ currentPage }} of {{ totalPages }} </span>
                    <Button variant="outline" size="sm" :disabled="currentPage === totalPages" @click="currentPage++"> Next </Button>
                </div>
            </div>
            <LogsDataTable :data="logs" :columns="columns" :loading="loading" />
        </div>

        <div class="block space-y-4 md:hidden">
            <!-- <LogsTableMobile :logs="logs" /> -->
        </div>
    </div>
</template>
