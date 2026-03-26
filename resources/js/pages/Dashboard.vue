<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { 
    Dialog, 
    DialogContent, 
    DialogDescription, 
    DialogFooter, 
    DialogHeader, 
    DialogTitle 
} from '@/components/ui/dialog';
import admin from '@/routes/admin';
import type { Appointment, BreadcrumbItem } from '@/types';
import UpcomingAppointments from './Dashboard/Components/UpcomingAppointments.vue';

const props = defineProps<{
    upcomingAppointments: {
        type: 'today' | 'tomorrow' | 'next_available' | 'none';
        items: Appointment[];
        next_date?: string;
    };
    monthlyStats: {
        total_appointments: number;
        monthly_revenue: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: admin.dashboard(),
    },
];

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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 sm:p-6 relative">
            <!-- Stats overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-6 bg-background border rounded-xl shadow-sm space-y-2">
                    <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Wszystkie wizyty w tym miesiącu</p>
                    <p class="text-3xl font-black text-foreground">{{ monthlyStats.total_appointments }}</p>
                </div>
                <div class="p-6 bg-background border rounded-xl shadow-sm space-y-2">
                    <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider">Przychód (Zakończone wizyty)</p>
                    <p class="text-3xl font-black text-primary">{{ monthlyStats.monthly_revenue }} zł</p>
                </div>
            </div>

            <!-- Upcoming Appointments Section -->
            <div class="bg-background border rounded-2xl p-6 shadow-sm relative z-10">
                <UpcomingAppointments 
                    :upcoming="upcomingAppointments" 
                    @delete="confirmDelete"
                />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Dialog :open="!!appointmentToDelete" @update:open="appointmentToDelete = null">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Czy na pewno chcesz usunąć tę wizytę?</DialogTitle>
                    <DialogDescription>
                        Ta operacja jest nieodwracalna. Wizyta zostanie trwale usunięta z kalendarza.
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
