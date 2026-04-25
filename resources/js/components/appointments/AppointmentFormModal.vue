<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { 
  Dialog, 
  DialogContent, 
  DialogHeader, 
  DialogTitle,
  DialogDescription 
} from '@/components/ui/dialog';
import AppointmentForm from '@/pages/Appointments/Parts/AppointmentForm.vue';
import type { Client, PriceItem, Appointment } from '@/types';
import { AppointmentStatus } from '@/types/appointment';
import admin from '@/routes/admin';

const props = defineProps<{
  open: boolean;
  appointment?: Appointment | null;
  clients: Client[];
  priceItems: PriceItem[];
  statuses: string[];
  initialDate?: Date;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'success'): void;
}>();

const isEdit = ref(false);

const form = useForm({
  client_id: '',
  start_time: '',
  status: AppointmentStatus.Scheduled,
  total_price: 0,
  notes: '',
  price_items: [] as number[],
});

// Initialize form based on creation/edit mode
watch(() => props.open, (isOpen) => {
  if (isOpen) {
    if (props.appointment) {
      isEdit.value = true;
      form.client_id = props.appointment.client_id.toString();
      form.start_time = format(new Date(props.appointment.start_time), "yyyy-MM-dd'T'HH:mm");
      form.status = props.appointment.status;
      form.total_price = props.appointment.total_price;
      form.notes = props.appointment.notes || '';
      form.price_items = props.appointment.price_items.map(item => item.id);
    } else {
      isEdit.value = false;
      const date = props.initialDate || new Date();
      // Set to next hour if it's "now"
      if (!props.initialDate) {
        date.setMinutes(0, 0, 0);
        date.setHours(date.getHours() + 1);
      }
      
      form.reset();
      form.start_time = format(date, "yyyy-MM-dd'T'HH:mm");
      form.status = AppointmentStatus.Scheduled;
    }
  }
});

const submit = () => {
  const action = isEdit.value 
    ? admin.appointments.update(props.appointment!.id)
    : admin.appointments.store();

  if (isEdit.value) {
    form.put(action, {
      onSuccess: () => {
        emit('update:open', false);
        emit('success');
      }
    });
  } else {
    form.post(action, {
      onSuccess: () => {
        emit('update:open', false);
        emit('success');
      }
    });
  }
};

const handleCancel = () => {
  emit('update:open', false);
};
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-[600px] max-h-[95vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>{{ isEdit ? 'Edytuj wizytę' : 'Nowa wizyta' }}</DialogTitle>
        <DialogDescription>
          {{ isEdit ? 'Zaktualizuj szczegóły istniejącej rezerwacji.' : 'Wprowadź dane, aby zarezerwować termin.' }}
        </DialogDescription>
      </DialogHeader>

      <AppointmentForm 
        :form="form"
        :clients="clients"
        :price-items="priceItems"
        :statuses="statuses"
        :is-edit="isEdit"
        @submit="submit"
        @cancel="handleCancel"
      />
    </DialogContent>
  </Dialog>
</template>
