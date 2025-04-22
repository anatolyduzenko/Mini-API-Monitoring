<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'; /* PartiallyEnd: #3632/scriptSetup.vue */
</script>

<template>
    <Dialog>
        <DialogTrigger> Select Endpoints </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Select Endpoints</DialogTitle>
                <DialogDescription> Select endpoints to display on the dashboard screen. </DialogDescription>
            </DialogHeader>

            <DialogFooter> Save changes </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
