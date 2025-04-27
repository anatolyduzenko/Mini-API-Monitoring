<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Trash2 } from 'lucide-vue-next';
import { defineEmits, defineProps, ref } from 'vue';

const emit = defineEmits(['confirmed', 'cancel']);

const props = defineProps({
    token: Object,
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const current_password = ref('');

function confirm() {
    emit('confirmed', {
        tokenId: props.token.id,
        password: current_password.value,
    });
}
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <button class="text-red-500" variant="outline">
                <component :is="Trash2" />
            </button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Confirmation</DialogTitle>
                <DialogDescription>
                    <p>Are you sure you want revoke this token?</p>
                    <p>Please enter your current password to confirm.</p>
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid grid-cols-4 items-center gap-2">
                    <Label for="current_password" class="text-right"> Current Password </Label>
                    <Input id="current_passowrd" default-value="" v-model="current_password" type="password" class="col-span-3" />
                    <InputError :message="props.errors.current_password" class="col-span-4" />
                </div>
            </div>
            <DialogFooter class="sm:justify-between">
                <DialogClose as-child>
                    <Button type="button" variant="secondary"> Close </Button>
                </DialogClose>
                <Button @click="confirm"> Confirm </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
