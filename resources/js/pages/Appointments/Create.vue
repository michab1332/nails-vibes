<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { Client, PriceItem, BreadcrumbItem} from '@/types';
import { AppointmentStatus } from '@/types';
import AppointmentForm from './Parts/AppointmentForm.vue';

const props = defineProps<{
    clients: Client[];
    priceItems: PriceItem[];
    statuses: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Wizyty',
        href: admin.appointments.index(),
    },
    {
        title: 'Nowa wizyta',
        href: admin.appointments.create(),
    },
];

// Set default start time to today, next hour
const defaultStartTime = new Date();
defaultStartTime.setMinutes(0, 0, 0);
defaultStartTime.setHours(defaultStartTime.getHours() + 1);

const form = useForm({
    client_id: '',
    start_time: format(defaultStartTime, "yyyy-MM-dd'T'HH:mm"),
    status: AppointmentStatus.Scheduled, // This is 'Zaplanowana'
    total_price: 0,
    notes: '',
    price_items: [] as number[],
});

const submit = () => {
    form.post(admin.appointments.store());
};

const handleCancel = () => {
    window.history.back();
};
</script>

<template>
    <Head title="Nowa wizyta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 sm:gap-6 sm:p-6 max-w-5xl mx-auto w-full">
            <Card>
                <CardHeader>
                    <CardTitle>Dodaj nową wizytę</CardTitle>
                    <CardDescription>
                        Wprowadź szczegóły wizyty, wybierz klientkę oraz usługi z cennika.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <AppointmentForm 
                        :form="form" 
                        :clients="clients" 
                        :price-items="priceItems"
                        :statuses="statuses"
                        @submit="submit"
                        @cancel="handleCancel"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
