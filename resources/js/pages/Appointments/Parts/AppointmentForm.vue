<script setup lang="ts">
import type {
    DateValue} from '@internationalized/date';
import {
    CalendarDate,
    getLocalTimeZone,
    today
} from '@internationalized/date';
import { format, parseISO, isValid } from 'date-fns';
import { pl } from 'date-fns/locale';
import {
    CalendarIcon,
    Check,
    ChevronsUpDown,
    Search,
    X,
    Clock,
    Plus
} from 'lucide-vue-next';
import { ref, computed, watch, onMounted } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger
} from '@/components/ui/popover';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';
import ClientForm from '@/pages/Clients/Parts/Form.vue';
import type { Client, PriceItem} from '@/types';
import { useAppointmentPrice } from '../Composables/useAppointmentPrice';

const props = defineProps<{
    form: any;
    clients: Client[];
    priceItems: PriceItem[];
    statuses: string[];
    isEdit?: boolean;
}>();

const emit = defineEmits<{
    (e: 'submit'): void;
}>();

// Client Selection Logic
const clientSearchQuery = ref('');
const isClientPopoverOpen = ref(false);
const isClientModalOpen = ref(false);

const filteredClients = computed(() => {
    if (!clientSearchQuery.value) {
return props.clients;
}

    return props.clients.filter(client =>
        client.name.toLowerCase().includes(clientSearchQuery.value.toLowerCase()) ||
        (client.phone_number && client.phone_number.includes(clientSearchQuery.value))
    );
});

const selectedClientLabel = computed(() => {
    const client = props.clients.find(c => c.id.toString() === props.form.client_id?.toString());

    return client ? `${client.name} ${client.phone_number ? `(${client.phone_number})` : ''}` : 'Wybierz klientkę...';
});

const onClientCreated = () => {
    isClientModalOpen.value = false;
};

// Auto-select newly added client
watch(() => props.clients, (newClients, oldClients) => {
    if (newClients && oldClients && newClients.length > oldClients.length) {
        const newestClient = [...newClients].sort((a, b) => b.id - a.id)[0];

        if (newestClient) {
            props.form.client_id = newestClient.id.toString();
        }
    }
}, { deep: true });

// Selected price items handling
const selectedPriceItems = ref<number[]>(props.form.price_items || []);

// Statuses display fix
const safeStatuses = computed(() => {
    return Array.isArray(props.statuses) ? props.statuses : [];
});

// Price calculation
const currentPriceRef = ref(props.form.total_price);
const { handleManualPriceChange } = useAppointmentPrice(
    selectedPriceItems,
    props.priceItems,
    currentPriceRef
);

watch(currentPriceRef, (newVal) => {
    props.form.total_price = newVal;
});

// Services Multi-select logic
const searchQuery = ref('');
const isServicesOpen = ref(false);

const filteredPriceItems = computed(() => {
    if (!searchQuery.value) {
return props.priceItems;
}

    return props.priceItems.filter(item =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const togglePriceItem = (id: number) => {
    const index = selectedPriceItems.value.indexOf(id);

    if (index > -1) {
        selectedPriceItems.value.splice(index, 1);
    } else {
        selectedPriceItems.value.push(id);
    }

    props.form.price_items = selectedPriceItems.value;
};

const selectedItemsLabels = computed(() => {
    if (selectedPriceItems.value.length === 0) {
return 'Wybierz usługi...';
}

    const items = props.priceItems.filter(item => selectedPriceItems.value.includes(item.id));

    if (items.length <= 2) {
        return items.map(item => item.name).join(', ');
    }

    return `Wybrano ${items.length} usług`;
});

// Advanced DateTime Picker logic
const dateValue = ref<DateValue | undefined>(undefined);
const hourValue = ref('12');
const minuteValue = ref('00');

onMounted(() => {
    if (props.form.start_time) {
        const parsed = parseISO(props.form.start_time);

        if (isValid(parsed)) {
            dateValue.value = new CalendarDate(
                parsed.getFullYear(),
                parsed.getMonth() + 1,
                parsed.getDate()
            );
            hourValue.value = format(parsed, 'HH');
            minuteValue.value = format(parsed, 'mm');
        }
    } else {
        dateValue.value = today(getLocalTimeZone());
    }
});

watch([dateValue, hourValue, minuteValue], ([newDate, newHour, newMinute]) => {
    if (newDate) {
        const dateStr = newDate.toString(); // YYYY-MM-DD
        props.form.start_time = `${dateStr}T${newHour}:${newMinute}`;
    }
});

const hours = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0'));
const minutes = Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'));

const goBack = () => {
    window.history.back();
};

const formatDateLabel = (val: DateValue | undefined) => {
    if (!val) {
return 'Wybierz termin wizyty...';
}

    const date = val.toDate(getLocalTimeZone());

    return `${format(date, 'd MMMM yyyy', { locale: pl })} o ${hourValue.value}:${minuteValue.value}`;
};

const formatPriceRange = (item: PriceItem) => {
    if (item.price_max === null) {
        return `${item.price_min} zł`;
    }

    if (item.price_min === item.price_max) {
        return `${item.price_min} zł`;
    }

    return `${item.price_min}-${item.price_max} zł`;
};
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <div class="space-y-6">

            <!-- Klientka -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label for="client_id" class="text-sm font-bold">Klientka</Label>
                    <Button
                        type="button"
                        variant="link"
                        size="sm"
                        class="h-auto p-0 text-xs text-primary font-bold"
                        @click="isClientModalOpen = true"
                    >
                        <Plus class="mr-1 h-3 w-3" />
                        Dodaj nową klientkę
                    </Button>
                </div>

                <Popover v-model:open="isClientPopoverOpen">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            role="combobox"
                            :aria-expanded="isClientPopoverOpen"
                            class="w-full justify-between font-normal h-11 px-3"
                            :class="{ 'border-destructive': form.errors.client_id }"
                        >
                            <span class="truncate">{{ selectedClientLabel }}</span>
                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="p-0 w-[var(--radix-popover-trigger-width)] max-w-[calc(100vw-2rem)]" align="start">
                        <div class="flex items-center border-b px-3 py-2">
                            <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                            <input
                                v-model="clientSearchQuery"
                                placeholder="Wyszukaj klientkę..."
                                class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                            />
                        </div>
                        <div class="max-h-[300px] overflow-y-auto p-1">
                            <div
                                v-for="client in filteredClients"
                                :key="client.id"
                                class="relative flex cursor-pointer select-none items-center rounded-sm px-3 py-3 text-sm outline-none hover:bg-accent hover:text-accent-foreground transition-colors border-b last:border-0"
                                @click="form.client_id = client.id.toString(); isClientPopoverOpen = false"
                            >
                                <div class="flex flex-col flex-1 pr-8">
                                    <span class="font-bold leading-tight">{{ client.name }}</span>
                                    <span v-if="client.phone_number" class="text-xs text-muted-foreground">{{ client.phone_number }}</span>
                                </div>
                                <div v-if="form.client_id === client.id.toString()" class="flex h-4 w-4 items-center justify-center shrink-0">
                                    <Check class="h-5 w-5 text-primary stroke-[3]" />
                                </div>
                            </div>
                            <div v-if="filteredClients.length === 0" class="py-10 text-center text-sm text-muted-foreground">
                                Brak klientek pasujących do wyszukiwania.
                            </div>
                        </div>
                    </PopoverContent>
                </Popover>
                <InputError :message="form.errors.client_id" />

                <!-- Modal Dodawania Klientki -->
                <Dialog v-model:open="isClientModalOpen">
                    <DialogContent class="sm:max-w-md">
                        <DialogHeader>
                            <DialogTitle>Dodaj nową klientkę</DialogTitle>
                            <DialogDescription>
                                Wpisz dane nowej klientki, aby móc przypisać ją do wizyty.
                            </DialogDescription>
                        </DialogHeader>
                        <ClientForm :on-success="onClientCreated" :redirect-back="true" />
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Termin wizyty (Fully Responsive Integrated Popover) -->
            <div class="space-y-2">
                <Label class="text-sm font-bold">Data i godzina wizyty</Label>
                <Popover>
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            :class="cn(
                                'w-full justify-start text-left font-normal h-11 px-3',
                                !dateValue && 'text-muted-foreground',
                                form.errors.start_time && 'border-destructive'
                            )"
                        >
                            <CalendarIcon class="mr-2 h-4 w-4" />
                            <span class="truncate">{{ formatDateLabel(dateValue) }}</span>
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0 flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x" align="start">
                        <!-- Calendar Section -->
                        <div class="p-2 sm:p-3">
                            <Calendar
                                v-model="dateValue"
                                initial-focus
                                locale="pl"
                                class="w-full sm:w-auto"
                            />
                        </div>

                        <!-- Time Picker Section -->
                        <div class="flex flex-col h-[280px] md:h-[340px] w-full md:w-[160px] bg-background">
                            <div class="p-3 border-b text-center text-xs font-bold text-muted-foreground uppercase tracking-wider bg-muted/30">
                                Wybierz godzinę
                            </div>
                            <div class="flex flex-1 overflow-hidden h-full">
                                <!-- Hours Scroll -->
                                <div class="flex-1 overflow-y-auto scrollbar-none hover:scrollbar-thin p-1 border-r">
                                    <div class="text-[10px] text-center text-muted-foreground mb-1 uppercase font-bold opacity-50 sticky top-0 bg-background py-1">Godz</div>
                                    <div class="flex flex-col gap-1">
                                        <Button
                                            v-for="h in hours"
                                            :key="h"
                                            variant="ghost"
                                            size="sm"
                                            class="w-full justify-center h-10 md:h-8 px-0 text-sm"
                                            :class="h === hourValue ? 'bg-primary text-primary-foreground hover:bg-primary font-bold' : ''"
                                            @click="hourValue = h"
                                        >
                                            {{ h }}
                                        </Button>
                                    </div>
                                </div>
                                <!-- Minutes Scroll -->
                                <div class="flex-1 overflow-y-auto scrollbar-none hover:scrollbar-thin p-1">
                                    <div class="text-[10px] text-center text-muted-foreground mb-1 uppercase font-bold opacity-50 sticky top-0 bg-background py-1">Min</div>
                                    <div class="flex flex-col gap-1">
                                        <Button
                                            v-for="m in minutes"
                                            :key="m"
                                            variant="ghost"
                                            size="sm"
                                            class="w-full justify-center h-10 md:h-8 px-0 text-sm"
                                            :class="m === minuteValue ? 'bg-primary text-primary-foreground hover:bg-primary font-bold' : ''"
                                            @click="minuteValue = m"
                                        >
                                            {{ m }}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 border-t bg-muted/10 text-center flex items-center justify-center gap-2">
                                <Clock class="h-4 w-4 text-primary" />
                                <span class="text-base font-bold text-primary">{{ hourValue }}:{{ minuteValue }}</span>
                            </div>
                        </div>
                    </PopoverContent>
                </Popover>
                <InputError :message="form.errors.start_time" />
            </div>

            <!-- Usługi (Responsive Multi-select) -->
            <div class="space-y-2">
                <Label class="text-sm font-bold">Usługi</Label>
                <Popover v-model:open="isServicesOpen">
                    <PopoverTrigger as-child>
                        <Button
                            variant="outline"
                            role="combobox"
                            :aria-expanded="isServicesOpen"
                            class="w-full justify-between font-normal h-11 px-3"
                        >
                            <span class="truncate">{{ selectedItemsLabels }}</span>
                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent
                        class="p-0 w-[var(--radix-popover-trigger-width)] max-w-[calc(100vw-2rem)]"
                        align="start"
                    >
                        <div class="flex items-center border-b px-3 py-2">
                            <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                            <input
                                v-model="searchQuery"
                                placeholder="Wyszukaj usługę..."
                                class="flex h-10 w-full rounded-md bg-transparent py-3 text-sm outline-none placeholder:text-muted-foreground"
                            />
                        </div>
                        <div class="max-h-[300px] overflow-y-auto p-1">
                            <div
                                v-for="item in filteredPriceItems"
                                :key="item.id"
                                class="relative flex cursor-pointer select-none items-center rounded-sm px-3 py-3 text-sm outline-none hover:bg-accent hover:text-accent-foreground transition-colors border-b last:border-0"
                                @click="togglePriceItem(item.id)"
                            >
                                <div class="flex flex-col flex-1 pr-8">
                                    <span class="font-bold leading-tight">{{ item.name }}</span>
                                    <span class="text-xs text-muted-foreground">{{ formatPriceRange(item) }}</span>
                                </div>
                                <div v-if="selectedPriceItems.includes(item.id)" class="flex h-4 w-4 items-center justify-center shrink-0">
                                    <Check class="h-5 w-5 text-primary stroke-[3]" />
                                </div>
                            </div>
                            <div v-if="filteredPriceItems.length === 0" class="py-10 text-center text-sm text-muted-foreground">
                                Brak usług pasujących do wyszukiwania.
                            </div>
                        </div>
                        <div v-if="selectedPriceItems.length > 0" class="border-t p-2">
                            <Button
                                variant="ghost"
                                size="sm"
                                class="w-full justify-center text-xs h-9 text-muted-foreground hover:text-destructive"
                                @click="selectedPriceItems = []; props.form.price_items = []"
                            >
                                <X class="mr-2 h-3 w-3" />
                                Wyczyść zaznaczenie
                            </Button>
                        </div>
                    </PopoverContent>
                </Popover>
                <InputError :message="form.errors.price_items" />
            </div>

            <!-- Cena i Status (Responsive Grid) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2 w-full">
                    <Label for="total_price" class="text-sm font-bold">Cena (zł)</Label>
                    <div class="relative">
                        <Input
                            id="total_price"
                            type="number"
                            v-model="form.total_price"
                            @input="handleManualPriceChange"
                            class="pl-9 h-11 text-lg font-bold"
                            :class="{ 'border-destructive': form.errors.total_price }"
                        />
                        <span class="absolute left-3 top-2.5 text-muted-foreground font-bold">zł</span>
                    </div>
                    <InputError :message="form.errors.total_price" />
                </div>

                <div class="space-y-2 w-full">
                    <Label for="status" class="text-sm font-bold">Status</Label>
                    <Select v-model="form.status">
                        <SelectTrigger id="status" class="w-full h-11" :class="{ 'border-destructive': form.errors.status }">
                            <SelectValue placeholder="Wybierz status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="status in safeStatuses" :key="status" :value="status">
                                {{ status }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.status" />
                </div>
            </div>

            <!-- Notatki -->
            <div class="space-y-2">
                <Label for="notes" class="text-sm font-bold">Notatki do wizyty</Label>
                <Textarea
                    id="notes"
                    v-model="form.notes"
                    placeholder="Wpisz opcjonalne uwagi..."
                    rows="4"
                    class="resize-none"
                />
                <InputError :message="form.errors.notes" />
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-6 border-t">
            <Button type="button" variant="outline" @click="goBack" class="w-full sm:w-36 h-11 font-medium">
                Anuluj
            </Button>
            <Button type="submit" :disabled="form.processing" class="w-full sm:w-36 h-11 font-bold text-base">
                {{ isEdit ? 'Zaktualizuj' : 'Zarezerwuj' }}
            </Button>
        </div>
    </form>
</template>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 20px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
</style>
