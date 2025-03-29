<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { columns } from './columns';

import LogsDataTable from '@/components/logs/LogsDataTable.vue';

const logs = ref([]);
const loading = ref(false);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(15);

const fetchLogs = async (page = 1, per_page = 15) => {
    try {
        loading.value = true;
        const response = await fetch(route('api.logs.index', { page: page, per_page: per_page }));
        const data = await response.json();
        logs.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
        perPage.value = data.per_page;
    } catch (error) {
        console.error('Error fetching logs:', error);
    }
};

watch([currentPage, perPage], ([newPage, newPerPage], [oldPage, oldPerPage]) => {
    if (newPerPage !== oldPerPage) {
        newPage = 1;
    }
    fetchLogs(newPage, newPerPage);
});

onMounted(() => {
    fetchLogs();
});
</script>

<template>
    <div class="">
        <div class="p-4">
            <LogsDataTable :data="logs" :columns="columns" :loading="loading" />
            <div class="flex flex-col items-center justify-end space-y-2 py-4 sm:flex-row sm:space-x-2 sm:space-y-0">
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
                                <SelectItem :value="25"> 20 </SelectItem>
                                <SelectItem :value="50"> 50 </SelectItem>
                                <SelectItem :value="100"> 100 </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div>
                    <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="currentPage--"> Previous </Button>
                    <span class="px-4"> Page {{ currentPage }} of {{ totalPages }} </span>
                    <Button variant="outline" size="sm" :disabled="currentPage === totalPages" @click="currentPage++"> Next </Button>
                </div>
            </div>
        </div>

        <div class="block space-y-4 md:hidden">
            <!-- <LogsTableMobile :logs="logs" /> -->
        </div>
    </div>
</template>
