<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { MoreHorizontal, Calendar, Clock, Phone, Instagram } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem, 
    DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';
import admin from '@/routes/admin';
import type { Appointment } from '@/types';
import AppointmentStatusBadge from './AppointmentStatusBadge.vue';

const props = defineProps<{
    appointment: Appointment;
}>();

const emit = defineEmits<{
    (e: 'delete', id: number): void;
    (e: 'changeStatus', id: number, status: string): void;
}>();

const formattedTime = format(new Date(props.appointment.start_time), 'HH:mm');
const formattedDate = format(new Date(props.appointment.start_time), 'd MMMM yyyy', { locale: pl });

const priceItemsNames = props.appointment.price_items.map(item => item.name).join(', ');
</script>

<template>
    <Card class="overflow-hidden transition-all hover:shadow-md border border-zinc-200 dark:border-zinc-800 bg-zinc-100/50 dark:bg-zinc-900/50 py-0 gap-0 shadow-sm">
        <CardContent class="p-5 sm:p-6 px-5 sm:px-6">
            <div class="flex justify-between items-start mb-5">
                <div class="flex flex-col">
                    <span class="text-3xl font-black text-primary tracking-tight">{{ formattedTime }}</span>
                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider mt-1">{{ formattedDate }}</span>
                </div>
                
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="icon" class="-mr-2 h-8 w-8 hover:bg-zinc-200 dark:hover:bg-zinc-800">
                            <MoreHorizontal class="h-4 w-4" />
                            <span class="sr-only">Menu</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem asChild>
                            <Link :href="admin.appointments.edit(appointment.id)">
                                Edytuj wizytę
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                            class="text-destructive focus:text-destructive"
                            @click="emit('delete', appointment.id)"
                        >
                            Usuń wizytę
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex flex-col gap-1">
                        <span class="font-bold text-lg leading-none">{{ appointment.client.name }}</span>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground font-medium">
                            <a 
                                v-if="appointment.client.phone_number" 
                                :href="`tel:${appointment.client.phone_number}`"
                                class="flex items-center gap-1 hover:text-primary transition-colors"
                            >
                                <Phone class="h-3 w-3" />
                                {{ appointment.client.phone_number }}
                            </a>
                            <a 
                                v-if="appointment.client.ig_name" 
                                :href="`https://instagram.com/${appointment.client.ig_name.replace('@', '')}`"
                                target="_blank"
                                class="flex items-center gap-1 hover:text-primary transition-colors"
                            >
                                <Instagram class="h-3 w-3" />
                                {{ appointment.client.ig_name.startsWith('@') ? appointment.client.ig_name : `@${appointment.client.ig_name}` }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/60 dark:bg-black/20 rounded-lg p-3 space-y-2 border border-zinc-200/50 dark:border-zinc-800/50">
                    <div class="flex items-start gap-2">
                        <div class="text-sm font-semibold text-foreground/90 flex-1">
                            {{ priceItemsNames || 'Brak wybranych usług' }}
                        </div>
                    </div>
                    
                    <div v-if="appointment.notes" class="flex items-start gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                        <p class="text-xs text-muted-foreground italic leading-relaxed">
                            {{ appointment.notes }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <AppointmentStatusBadge :status="appointment.status" />
                    <div class="flex flex-col items-end">
                        <span class="text-xl font-black text-foreground">{{ appointment.total_price }} zł</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
