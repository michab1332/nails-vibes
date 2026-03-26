<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { format, parseISO } from 'date-fns';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { Appointment, Client, PriceItem, BreadcrumbItem } from '@/types';
import AppointmentForm from './Parts/AppointmentForm.vue';

const props = defineProps<{
    appointment: Appointment;
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
        title: 'Edycja wizyty',
        href: admin.appointments.edit(props.appointment.id),
    },
];

const form = useForm({
    client_id: props.appointment.client_id.toString(),
    start_time: format(parseISO(props.appointment.start_time), "yyyy-MM-dd'T'HH:mm"),
    status: props.appointment.status,
    total_price: props.appointment.total_price,
    notes: props.appointment.notes || '',
    price_items: props.appointment.price_items.map(item => item.id),
});

const submit = () => {
    form.put(admin.appointments.update(props.appointment.id));
};
</script>

<template>
    <Head title="Edycja wizyty" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 sm:gap-6 sm:p-6 max-w-5xl mx-auto w-full">
            <Card>
                <CardHeader>
                    <CardTitle>Edytuj wizytę</CardTitle>
                    <CardDescription>
                        Zaktualizuj szczegóły wizyty, zmień status lub usługi.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <AppointmentForm 
                        :form="form" 
                        :clients="clients" 
                        :price-items="priceItems"
                        :statuses="statuses"
                        is-edit
                        @submit="submit"
                    />
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
