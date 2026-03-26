<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { format, isToday, isTomorrow, parseISO } from 'date-fns';
import { pl } from 'date-fns/locale';
import { Plus, CalendarOff } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { 
    Dialog, 
    DialogContent, 
    DialogDescription, 
    DialogFooter, 
    DialogHeader, 
    DialogTitle 
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { Appointment, BreadcrumbItem } from '@/types';
import AppointmentCard from './Components/AppointmentCard.vue';

const props = defineProps<{
    appointments: Appointment[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Wizyty',
        href: admin.appointments.index(),
    },
];

const groupedAppointments = computed(() => {
    const groups: Record<string, Appointment[]> = {};

    props.appointments.forEach((appointment) => {
        const date = parseISO(appointment.start_time);
        let dateKey = '';

        if (isToday(date)) {
            dateKey = 'Dzisiaj';
        } else if (isTomorrow(date)) {
            dateKey = 'Jutro';
        } else {
            dateKey = format(date, 'd MMMM yyyy', { locale: pl });
        }

        if (!groups[dateKey]) {
            groups[dateKey] = [];
        }

        groups[dateKey].push(appointment);
    });

    return groups;
});

const appointmentToDelete = ref<number | null>(null);

const confirmDelete = (id: number) => {
    appointmentToDelete.value = id;
};

const deleteAppointment = () => {
    if (appointmentToDelete.value) {
        router.delete(admin.appointments.destroy(appointmentToDelete.value), {
            onSuccess: () => {
                appointmentToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Wizyty" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4 sm:gap-6 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Kalendarz wizyt</h1>
                    <p class="text-sm text-muted-foreground">Zarządzaj swoimi zapisami i planuj czas pracy.</p>
                </div>
                <Button asChild>
                    <Link :href="admin.appointments.create()">
                        <Plus class="mr-2 h-4 w-4" />
                        Nowa wizyta
                    </Link>
                </Button>
            </div>

            <div v-if="appointments.length > 0" class="space-y-8">
                <div v-for="(group, dateLabel) in groupedAppointments" :key="dateLabel" class="space-y-4">
                    <h2 class="text-lg font-semibold border-b pb-2 text-muted-foreground sticky top-0 bg-background/95 backdrop-blur z-10 py-2">
                        {{ dateLabel }}
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <AppointmentCard 
                            v-for="appointment in group" 
                            :key="appointment.id" 
                            :appointment="appointment"
                            @delete="confirmDelete"
                        />
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-20 text-center border-2 border-dashed rounded-xl bg-muted/30">
                <div class="rounded-full bg-muted p-6 mb-4">
                    <CalendarOff class="h-10 w-10 text-muted-foreground" />
                </div>
                <h3 class="text-xl font-semibold">Brak zaplanowanych wizyt</h3>
                <p class="text-muted-foreground max-w-sm mt-2">
                    Twoja lista jest pusta. Kliknij przycisk powyżej, aby dodać swój pierwszy zapis.
                </p>
                <Button asChild variant="outline" class="mt-6">
                    <Link :href="admin.appointments.create()">
                        Dodaj pierwszą wizytę
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="!!appointmentToDelete" @update:open="appointmentToDelete = null">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Czy na pewno chcesz usunąć tę wizytę?</DialogTitle>
                    <DialogDescription>
                        Ta operacja jest nieodwracalna. Wszystkie dane dotyczące tej wizyty zostaną trwale usunięte.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="appointmentToDelete = null">
                        Anuluj
                    </Button>
                    <Button variant="destructive" @click="deleteAppointment">
                        Usuń wizytę
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
