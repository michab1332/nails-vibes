<script setup lang="ts">
import { computed } from 'vue';
import { format, parseISO } from 'date-fns';
import { pl } from 'date-fns/locale';
import { Calendar, ArrowRight, CalendarDays, Coffee, Sparkles } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import admin from '@/routes/admin';
import type { Appointment } from '@/types';
import AppointmentCard from '@/pages/Appointments/Components/AppointmentCard.vue';

const props = defineProps<{
    upcoming: {
        type: 'today' | 'tomorrow' | 'next_available' | 'none';
        items: Appointment[];
        next_date?: string;
    };
}>();

const emit = defineEmits<{
    (e: 'delete', id: number): void;
}>();

const title = computed(() => {
    if (props.upcoming.type === 'today') return 'Dzisiejsze rezerwacje';
    if (props.upcoming.type === 'tomorrow') return 'Jutrzejsze rezerwacje';
    if (props.upcoming.type === 'next_available' && props.upcoming.next_date) {
        return `Kolejna wizyta: ${format(parseISO(props.upcoming.next_date), 'EEEE, d MMMM', { locale: pl })}`;
    }
    return 'Brak nadchodzących wizyt';
});

const description = computed(() => {
    if (props.upcoming.type === 'today') return 'Twoje zaplanowane wizyty na pozostałą część dnia.';
    if (props.upcoming.type === 'tomorrow') return 'Dzisiaj już odpoczywasz! Oto co czeka Cię jutro.';
    if (props.upcoming.type === 'next_available') return 'Cisza przed burzą! Twoja najbliższa rezerwacja wypada nieco później.';
    return 'Twoja lista jest pusta. Czas na przerwę lub nowe zapisy!';
});
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-lg bg-primary/10 text-primary transition-colors">
                        <Calendar v-if="upcoming.type === 'today'" class="h-5 w-5" />
                        <Coffee v-else-if="upcoming.type === 'tomorrow' || upcoming.type === 'next_available'" class="h-5 w-5" />
                        <Sparkles v-else class="h-5 w-5" />
                    </div>
                    <h2 class="text-xl font-bold tracking-tight capitalize">{{ title }}</h2>
                </div>
                <p class="text-sm text-muted-foreground">{{ description }}</p>
            </div>
            
            <Button v-if="upcoming.type !== 'none'" asChild variant="outline" size="sm" class="hidden sm:flex">
                <Link :href="admin.appointments.index()">
                    Zobacz cały kalendarz
                    <ArrowRight class="ml-2 h-4 w-4" />
                </Link>
            </Button>
        </div>

        <div v-if="upcoming.items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <AppointmentCard 
                v-for="appointment in upcoming.items" 
                :key="appointment.id" 
                :appointment="appointment"
                @delete="id => emit('delete', id)"
            />
        </div>

        <div v-else class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed rounded-2xl bg-muted/30">
            <div class="rounded-full bg-background p-4 shadow-sm mb-4">
                <CalendarDays class="h-8 w-8 text-muted-foreground/50" />
            </div>
            <h3 class="text-lg font-semibold">Wszystko gotowe!</h3>
            <p class="text-muted-foreground max-w-xs mt-2 text-sm">
                Nie masz żadnych nadchodzących wizyt na ten moment. 
            </p>
            <Button asChild class="mt-6" size="sm">
                <Link :href="admin.appointments.create()">
                    Dodaj nową wizytę
                </Link>
            </Button>
        </div>

        <Button v-if="upcoming.type !== 'none'" asChild variant="outline" class="w-full sm:hidden">
            <Link :href="admin.appointments.index()">
                Pełny kalendarz
                <ArrowRight class="ml-2 h-4 w-4" />
            </Link>
        </Button>
    </div>
</template>
