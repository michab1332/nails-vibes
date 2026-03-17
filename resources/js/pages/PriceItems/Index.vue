<template>
    <Head title="Cennik" />

    <AppLayout :breadcrumbs="breadcrumbs">
        ss
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';

import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cennik',
        href: admin.priceItems.index(),
    },
];


</script>
