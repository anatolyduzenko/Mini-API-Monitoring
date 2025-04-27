<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { TransitionRoot } from '@headlessui/vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { capitalize, ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import TokenRevokeConfirmation from '@/components/TokenRevokeConfirmation.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { AlertCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Token settings',
        href: '/settings/token',
    },
];

const currentPasswordInput = ref<HTMLInputElement | null>(null);

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const errors = ref({});

const form = useForm({
    user_id: user.id,
    current_password: '',
    token_name: '',
});

const requestHeaders = new Headers();
requestHeaders.append('Content-Type', 'application/json');
requestHeaders.append('Accept', 'application/json');
requestHeaders.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

const generateNewToken = () => {
    form.post(route('token.generate'), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            toast.success('Token has been created.');
        },
        onError: (errors: any) => {
            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};

const deleteToken = async ({ tokenId, password }) => {
    try {
        const response = await fetch(route('token.revoke', [tokenId]), {
            method: 'DELETE',
            headers: requestHeaders,
            body: JSON.stringify({ current_password: password }),
        });
        if (response.ok) {
            toast.success('Token has been revoked.');
            setTimeout(() => {
                router.visit('/settings/token');
            }, 1000);
        } else if (response.status === 422) {
            const data = await response.json();
            toast.error(data.message || 'Incorrect password');
            errors.value.current_password = data.errors.current_password[0];
        } else {
            console.error('Unexpected error:', response.status);
        }
    } catch (error) {
        console.error('Error deleting token:', error);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Token settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Manage Token" description="Manage tokens to access the system endpoints." />

                <form @submit.prevent="generateNewToken" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <Input
                            id="current_password"
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="current-password"
                            placeholder="Current password"
                        />
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="token_name">Token Type</Label>
                        <Select id="token_name" ref="tokeTypeInput" v-model="form.token_name" type="text" class="mt-1 block w-full">
                            <SelectTrigger class="w-[180px]">
                                <SelectValue placeholder="Select a token type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem value="application">Application</SelectItem>
                                    <SelectItem value="prometheus">Prometheus</SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.token_name" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Generate Token</Button>

                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-neutral-600">Saved</p>
                        </TransitionRoot>
                    </div>

                    <div v-if="page.props.newToken" class="flex items-center gap-4">
                        <Alert>
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Token has been created! Please copy the value below.<br />It is displayed only once.</AlertTitle>
                            <AlertDescription>
                                {{ page.props.newToken }}
                            </AlertDescription>
                        </Alert>
                    </div>
                </form>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Token Type</TableHead>
                            <TableHead>Expires At</TableHead>
                            <TableHead>Revoke</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="token in page.props.tokens">
                            <TableCell>{{ capitalize(token.name) }}</TableCell>
                            <TableCell>{{ token.expires_at }}</TableCell>
                            <TableCell>
                                <TokenRevokeConfirmation :token="token" :errors="errors" @confirmed="deleteToken" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
