<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import { 
  SimpleCalendarRoot, 
  SimpleCalendarHeader, 
  SimpleCalendarGrid,
  CalendarEvent 
} from '@/components/ui/simple-calendar';
import { Button } from '@/components/ui/button';
import { 
  ChevronLeft, 
  ChevronRight, 
  Plus, 
  Calendar as CalendarIcon,
  MoreHorizontal
} from 'lucide-vue-next';
import { pl } from 'date-fns/locale';
import { format, startOfMonth, endOfMonth, isSameDay, parseISO } from 'date-fns';
import { Appointment } from '@/types/appointment';
import type { Client, PriceItem } from '@/types';
import AppointmentStatusBadge from '@/components/appointments/AppointmentStatusBadge.vue';
import AppointmentFormModal from '@/components/appointments/AppointmentFormModal.vue';
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';

interface Props {
  appointments: Appointment[];
  clients: Client[];
  priceItems: PriceItem[];
  statuses: string[];
  filters: any;
}

const props = defineProps<Props>();

const breadcrumbs = [
  { title: 'Terminarz', href: admin.appointments.calendar() }
];

const selectedDate = ref(new Date());

// Modal state
const isModalOpen = ref(false);
const selectedAppointment = ref<Appointment | null>(null);

const openCreateModal = () => {
  selectedAppointment.value = null;
  isModalOpen.value = true;
};

const openEditModal = (apt: Appointment) => {
  selectedAppointment.value = apt;
  isModalOpen.value = true;
};

const calendarEvents = computed<CalendarEvent<Appointment>[]>(() => 
  props.appointments.map(apt => ({
    id: apt.id,
    start: parseISO(apt.start_time),
    end: parseISO(apt.start_time),
    data: apt
  }))
);

const handleMonthChange = (newDate: Date) => {
  const start = startOfMonth(newDate);
  const end = endOfMonth(newDate);
  
  router.reload({
    data: {
      from_date: format(start, 'yyyy-MM-dd'),
      to_date: format(end, 'yyyy-MM-dd'),
    },
    only: ['appointments'],
    preserveState: true,
  });
};

const selectedDayAppointments = computed(() => {
  return props.appointments.filter(apt => 
    isSameDay(parseISO(apt.start_time), selectedDate.value)
  ).sort((a, b) => a.start_time.localeCompare(b.start_time));
});

const formatTime = (isoString: string) => {
  return format(parseISO(isoString), 'HH:mm');
};
</script>

<template>
  <Head title="Kalendarz Wizyt" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col h-full bg-background">
      <SimpleCalendarRoot 
        v-model="selectedDate" 
        :events="calendarEvents"
        :config="{ locale: pl, weekStartsOn: 1 }"
      >
        <SimpleCalendarHeader v-slot="{ label, next, prev, today }">
          <div class="flex items-center justify-between px-4 py-3 border-b sticky top-0 bg-background z-10">
            <div class="flex flex-col">
              <h1 class="text-xl font-bold tracking-tight">{{ label }}</h1>
              <p class="text-xs text-muted-foreground">{{ format(selectedDate, 'EEEE, d MMMM', { locale: pl }) }}</p>
            </div>
            
            <div class="flex items-center gap-2">
              <Button variant="outline" size="sm" @click="today" class="h-8">Dzisiaj</Button>
              <div class="flex items-center rounded-md border bg-muted/50 p-0.5">
                <Button variant="ghost" size="icon" @click="() => { prev(); handleMonthChange(selectedDate); }" class="h-7 w-7">
                  <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button variant="ghost" size="icon" @click="() => { next(); handleMonthChange(selectedDate); }" class="h-7 w-7">
                  <ChevronRight class="h-4 w-4" />
                </Button>
              </div>
              <Button size="icon" class="h-8 w-8 rounded-full" @click="openCreateModal">
                <Plus class="h-4 w-4" />
              </Button>
            </div>
          </div>
        </SimpleCalendarHeader>

        <div class="border-b shadow-sm overflow-hidden bg-card">
          <SimpleCalendarGrid />
        </div>

        <div class="flex-1 overflow-y-auto">
          <div class="px-4 py-4 space-y-4">
            <div v-if="selectedDayAppointments.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground">
              <CalendarIcon class="h-12 w-12 mb-2 opacity-20" />
              <p>Brak zaplanowanych wizyt</p>
            </div>

            <div v-else class="space-y-1">
              <div 
                v-for="apt in selectedDayAppointments" 
                :key="apt.id"
                class="group relative flex items-start gap-4 py-3 border-b border-border/40 last:border-0 hover:bg-accent/50 transition-colors rounded-lg px-2 -mx-2"
              >
                <div class="flex flex-col items-center justify-start pt-1 min-w-[50px]">
                  <span class="text-sm font-bold">{{ formatTime(apt.start_time) }}</span>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-2">
                    <h4 class="text-sm font-semibold truncate">{{ apt.client.name }}</h4>
                    <AppointmentStatusBadge :status="apt.status" />
                  </div>
                  <p class="text-xs text-muted-foreground mt-0.5 truncate">
                    {{ apt.price_items.map(i => i.name).join(' • ') }}
                  </p>
                </div>

                <DropdownMenu>
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon" class="h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity">
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="openEditModal(apt)">
                      Edytuj
                    </DropdownMenuItem>
                    <DropdownMenuItem class="text-destructive" @click="() => { if(confirm('Na pewno usunąć?')) router.delete(admin.appointments.destroy(apt.id)) }">
                      Usuń
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </div>
            </div>
          </div>
        </div>
      </SimpleCalendarRoot>

      <AppointmentFormModal 
        v-model:open="isModalOpen"
        :appointment="selectedAppointment"
        :clients="clients"
        :price-items="priceItems"
        :statuses="statuses"
        :initial-date="selectedDate"
      />
    </div>
  </AppLayout>
</template>
