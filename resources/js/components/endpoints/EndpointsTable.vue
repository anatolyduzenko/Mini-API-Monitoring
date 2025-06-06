<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';

import EndpointForm from '@/components/endpoints/EndpointForm.vue';
import EndpointsTableDesktop from '@/components/endpoints/EndpointsTableDesktop.vue';
import EndpointsTableMobile from '@/components/endpoints/EndpointsTableMobile.vue';

import { Pagination, PaginationFirst, PaginationLast, PaginationNext, PaginationPrev } from '@/components/ui/pagination';

const endpoints = ref([]);
const errors = ref({});
const currentUser = ref(null);
const showModal = ref(false);
const isEditing = ref(false);
const selectedEndpoint = ref(null);
const page = ref(1);
const totalItems = ref(10);
const perPage = ref(10);

const requestHeaders = new Headers();
requestHeaders.append('Content-Type', 'application/json');
requestHeaders.append('Accept', 'application/json');
requestHeaders.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

const fetchEndpoints = async (page = 1) => {
    try {
        const response = await fetch(route('api.endpoints.index', { page: page }));
        const data = await response.json();
        endpoints.value = data.data;
        totalItems.value = data.total;
        perPage.value = data.per_page;
    } catch (error) {
        console.error('Error fetching endpoints:', error);
    }
};

const fetchCurrentUser = async () => {
    try {
        const response = await fetch(route('api.user'));
        currentUser.value = await response.json();
    } catch (error) {
        console.error('Error fetching user:', error);
    }
};

const openCreateForm = () => {
    isEditing.value = false;
    selectedEndpoint.value = { id: null, name: '', url: '', method: 'GET', check_interval: 5 };
    showModal.value = true;
};

const openEditForm = (endpoint) => {
    isEditing.value = true;
    selectedEndpoint.value = { ...endpoint };
    showModal.value = true;
};

const submitForm = async (endpoint) => {
    errors.value = {};
    try {
        const url = endpoint.id ? route('api.endpoints.update', [endpoint.id]) : route('api.endpoints.store');

        const method = endpoint.id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method: method,
            headers: requestHeaders,
            body: JSON.stringify(endpoint),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422) {
                errors.value = data.errors;
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        } else {
            showModal.value = false;
            fetchEndpoints();
        }
    } catch (error) {
        console.error('Error saving endpoint:', error);
    }
};

const deleteEndpoint = async (endpoint) => {
    if (!confirm('Are you sure you want to delete this endpoint?')) return;

    try {
        await fetch(route('api.endpoints.destroy', [endpoint.id]), {
            method: 'DELETE',
            headers: requestHeaders,
        });
        fetchEndpoints();
    } catch (error) {
        console.error('Error deleting endpoint:', error);
    }
};

watch(page, (page) => {
    fetchEndpoints(page);
});

onMounted(() => {
    fetchEndpoints();
    fetchCurrentUser();
});
</script>

<template>
    <div>
        <div class="p-4">
            <button @click="openCreateForm" class="rounded bg-blue-500 px-4 py-2 text-white">+ Add Endpoint</button>
        </div>

        <div class="hidden p-4 md:block">
            <EndpointsTableDesktop :endpoints="endpoints" @edit="openEditForm" @delete="deleteEndpoint" />
        </div>

        <div class="block space-y-4 md:hidden">
            <EndpointsTableMobile :endpoints="endpoints" @edit="openEditForm" @delete="deleteEndpoint" />
        </div>

        <div v-if="totalItems > 10" class="p-4">
            <Pagination v-model:page="page" :items-per-page="10" :total="totalItems" :sibling-count="1" show-edges :default-page="2">
                <PaginationFirst />
                <PaginationPrev />
                <PaginationNext />
                <PaginationLast />
            </Pagination>
        </div>

        <EndpointForm
            :show="showModal"
            :endpoint="selectedEndpoint"
            :errors="errors"
            :current-user="currentUser"
            @close="showModal = false"
            @submit="submitForm"
        />
    </div>
</template>
