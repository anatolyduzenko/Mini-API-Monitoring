<script setup lang="ts">
    import { ref, onMounted } from "vue";
    import { route } from "ziggy-js";
    
    import { TailwindPagination } from 'laravel-vue-pagination';

    import EndpointForm from "@/components/endpoints/EndpointForm.vue";
    import EndpointsTableDesktop from "@/components/endpoints/EndpointsTableDesktop.vue";
    import EndpointsTableMobile from "@/components/endpoints/EndpointsTableMobile.vue";

    const endpoints = ref([]);
    const errors = ref({});
    const showModal = ref(false);
    const isEditing = ref(false);
    const selectedEndpoint = ref(null);

    const requestHeaders = new Headers();
    requestHeaders.append("Content-Type", "application/json");
    requestHeaders.append("Accept", "application/json");
    requestHeaders.append("X-CSRF-TOKEN",  document.querySelector('meta[name="csrf-token"]').getAttribute('content'));            

    const fetchEndpoints = async (page = 1) => {
        try {
            const response = await fetch(route("api.endpoints.index", {page: page})); 
            endpoints.value = await response.json();
        } catch (error) {
            console.error("Error fetching endpoints:", error);
        }
    };

    const openCreateForm = () => {
        isEditing.value = false;
        selectedEndpoint.value = { id: null, name: "", url: "", method: "GET", check_interval: 5 };
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
            const url = endpoint.id
                ? route("api.endpoints.update", [ endpoint.id ])
                : route("api.endpoints.store");

            const method = endpoint.id ? "PUT" : "POST";
            console.log(requestHeaders);
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
            console.error("Error saving endpoint:", error);
        }
    };

    const deleteEndpoint = async (endpoint) => {
        if (!confirm("Are you sure you want to delete this endpoint?")) return;

        try {
            
            await fetch(route("api.endpoints.destroy", [ endpoint.id ]), { 
                method: "DELETE",
                headers: requestHeaders,
            });
            fetchEndpoints();
        } catch (error) {
            console.error("Error deleting endpoint:", error);
        }
    };

    onMounted(fetchEndpoints);
</script>


<template>
    <div >
        <div class="p-4">
            <button @click="openCreateForm" class="bg-blue-500 text-white px-4 py-2 rounded">+ Add Endpoint</button>
        </div>
        <div class="p-4">
            <TailwindPagination
                :data="endpoints"
                @pagination-change-page="fetchEndpoints"
            />
        </div>
        <div class="hidden md:block">
            <EndpointsTableDesktop :endpoints="endpoints" @edit="openEditForm" @delete="deleteEndpoint" />
        </div>

        <div class="block md:hidden space-y-4">
            <EndpointsTableMobile :endpoints="endpoints" @edit="openEditForm" @delete="deleteEndpoint" />
        </div>
        

        <EndpointForm
            :show="showModal"
            :endpoint="selectedEndpoint"
            :errors="errors"
            @close="showModal = false"
            @submit="submitForm"
        />        

    </div>
</template>