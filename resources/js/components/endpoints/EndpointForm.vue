<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
/// import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

import { nextTick, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps(['show', 'endpoint', 'currentUser']);
const emit = defineEmits(['close', 'submit']);

const editableEndpoint = ref({
    id: null,
    name: '',
    url: '',
    method: 'GET',
    check_interval: 5,
    alert_threshold: 90,
    headers: '',
    body: '',
    user_id: props.currentUser ? props.currentUser.id : null,
    auth_type: '',
    auth_token: '',
    auth_token_name: '',
    auth_url: '',
    username: '',
    password: '',
});
const errors = ref({ name: '', url: '' });
const requestTypes = ref({});
const authTypes = ref({});

const jsonErrors = reactive({
    headers: '',
    body: '',
});

const headersRef = ref(null);
const bodyRef = ref(null);

watch(
    () => props.endpoint,
    (newValue) => {
        editableEndpoint.value = newValue
            ? { ...newValue, user_id: props.currentUser ? props.currentUser.id : null }
            : {
                  id: null,
                  name: '',
                  url: '',
                  method: 'GET',
                  check_interval: 5,
                  alert_threshold: 90,
                  headers: '',
                  body: '',
                  user_id: props.currentUser ? props.currentUser.id : null,
                  auth_token: '',
                  auth_toke_name: '',
                  auth_type: '',
                  auth_url: '',
                  username: '',
                  password: '',
              };
    },
    { immediate: true },
);

const fetchRequestTypes = async () => {
    try {
        const response = await fetch(route('api.requestTypes.index'));
        const data = await response.json();
        requestTypes.value = data;
    } catch (error) {
        console.error('Error fetching request types:', error);
    }
};

const fetchAuthTypes = async () => {
    try {
        const response = await fetch(route('api.authTypes.index'));
        const data = await response.json();
        authTypes.value = data;
    } catch (error) {
        console.error('Error fetching auth types:', error);
    }
};

const submitForm = () => {
    emit('submit', editableEndpoint.value);
};

const validateJson = (field) => {
    jsonErrors[field] = '';
    let jsonValue = '';
    const inputValue = editableEndpoint.value[field];

    if (inputValue === '') {
        return;
    }

    const textarea = field === 'headers' ? headersRef.value : bodyRef.value;

    try {
        jsonValue = JSON.parse(inputValue);
        editableEndpoint.value[field] = JSON.stringify(jsonValue, null, 2);
    } catch (e) {
        jsonErrors[field] = e.message;

        const match = e.message.match(/position\s(\d+)/);
        if (match && match[1]) {
            const pos = parseInt(match[1], 10);
            if (!isNaN(pos)) {
                nextTick(() => {
                    textarea.setSelectionRange(pos, pos + 1);
                    textarea.focus();
                });
            }
        }
    }
};

onMounted(() => {
    fetchAuthTypes();
    fetchRequestTypes();
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
        <div :class="['rounded bg-white p-6 shadow-lg', editableEndpoint.method === 'POST' ? 'w-[60%]' : 'w-[30%]']">
            <h3 class="mb-4 text-lg font-bold">{{ editableEndpoint.id ? 'Edit Endpoint' : 'Add New Endpoint' }}</h3>

            <Tabs default-value="basic">
                <TabsList>
                    <TabsTrigger value="basic">Basic Parameters</TabsTrigger>
                    <TabsTrigger value="authorization">Authorization</TabsTrigger>
                </TabsList>
                <TabsContent value="basic">
                    <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                        <div
                            :class="[
                                'grid auto-rows-max gap-4',
                                editableEndpoint.method === 'POST' ? 'grid-cols-1 md:grid-cols-2 xl:grid-cols-2' : 'grid-cols-1',
                            ]"
                        >
                            <div>
                                <Label class="mb-2 block" htmlFor="name">Name:</Label>
                                <p v-if="errors.name" class="error">{{ errors.name[0] }}</p>
                                <input v-model="editableEndpoint.name" id="name" type="text" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="url">URL:</Label>
                                <p v-if="errors.url" class="error">{{ errors.url[0] }}</p>
                                <input v-model="editableEndpoint.url" id="url" type="text" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="method">Method:</Label>
                                <Select v-model="editableEndpoint.method" id="method" :defaultValue="editableEndpoint.method" class="h-[37px] p-2">
                                    <SelectTrigger class="mb-3 w-[180px]">
                                        <SelectValue placeholder="Select Request Type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="code in requestTypes" :key="code.id" :value="code.id"> {{ code.name }} </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>

                                <Label class="mb-2 block" htmlFor="interval">Check Interval (minutes):</Label>
                                <input v-model="editableEndpoint.check_interval" id="interval" type="number" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="threshold">Alert threshold (percent):</Label>
                                <input
                                    v-model="editableEndpoint.alert_threshold"
                                    id="threshold"
                                    type="number"
                                    class="mb-3 w-full rounded border p-2"
                                />
                            </div>
                            <div v-if="editableEndpoint.method === 'POST'">
                                <Label class="mb-2 block" htmlFor="headers">Headers (JSON):</Label>
                                <textarea
                                    id="headers"
                                    ref="headersRef"
                                    v-model="editableEndpoint.headers"
                                    class="mb-3 h-[30%] w-full rounded border p-2"
                                    @input="() => validateJson('headers')"
                                />
                                <div v-if="jsonErrors.headers" style="color: red">{{ jsonErrors.headers }}</div>

                                <Label class="mb-2 block" htmlFor="body">Body (JSON):</Label>
                                <textarea
                                    id="body"
                                    ref="bodyRef"
                                    v-model="editableEndpoint.body"
                                    class="mb-3 h-[30%] w-full rounded border p-2"
                                    @input="() => validateJson('body')"
                                />
                                <div v-if="jsonErrors.body" style="color: red">{{ jsonErrors.body }}</div>
                            </div>
                        </div>
                    </div>
                </TabsContent>
                <TabsContent value="authorization">
                    <div class="flex flex-1 flex-col gap-4 rounded-xl p-4">
                        <div class="grid auto-rows-max grid-cols-1 gap-4">
                            <div>
                                <Label class="mb-2 block" htmlFor="password">Authorization type:</Label>
                                <Select
                                    v-model="editableEndpoint.auth_type"
                                    id="auth_type"
                                    :defaultValue="editableEndpoint?.auth_type ?? 'none'"
                                    class="h-[37px] p-2"
                                >
                                    <SelectTrigger class="mb-3 w-[180px]">
                                        <SelectValue placeholder="Select Authentication Type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem v-for="code in authTypes" :key="code.id" :value="code.id"> {{ code.name }} </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <!-- <div class="border p-2"> -->
                                <Label class="mb-2 block" htmlFor="username">Authorization URL:</Label>
                                <input v-model="editableEndpoint.auth_url" id="auth_url" type="text" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="username">Username:</Label>
                                <input v-model="editableEndpoint.username" id="username" type="text" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="password">Password:</Label>
                                <input v-model="editableEndpoint.password" id="password" type="password" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="username">Token:</Label>
                                <input v-model="editableEndpoint.auth_token" id="token" type="password" class="mb-3 w-full rounded border p-2" />

                                <Label class="mb-2 block" htmlFor="username">Token Name:</Label>
                                <input v-model="editableEndpoint.auth_token_name" id="token" type="text" class="mb-3 w-full rounded border p-2" />
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </TabsContent>
            </Tabs>
            <div class="mt-4 flex justify-between">
                <button @click="emit('close')" class="rounded bg-gray-400 px-4 py-2 text-white">Cancel</button>
                <button @click="submitForm" class="rounded bg-blue-500 px-4 py-2 text-white">
                    {{ editableEndpoint.id ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</template>
