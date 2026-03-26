<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search, X, Calendar as CalendarIcon, Filter, FilterX, Check } from 'lucide-vue-next';
import { format } from 'date-fns';
import { pl } from 'date-fns/locale';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Calendar } from '@/components/ui/calendar';
import { Badge } from '@/components/ui/badge';
import type { Client, AppointmentStatus } from '@/types';
import admin from '@/routes/admin';

const props = defineProps<{
    filters: {
        search?: string;
        scope?: 'current' | 'all';
        from_date?: string;
        to_date?: string;
        clients?: number[];
        statuses?: string[];
    };
    clients: Client[];
    statuses: AppointmentStatus[];
}>();

// Helper to ensure we always have numbers for IDs
const parseClientIds = (clients: any): number[] => {
    if (!clients) return [];
    const arr = Array.isArray(clients) ? clients : [clients];
    return arr.map(id => Number(id)).filter(id => !isNaN(id));
};

const localFilters = ref({
    search: props.filters.search || '',
    scope: props.filters.scope || 'current',
    from_date: props.filters.from_date || '',
    to_date: props.filters.to_date || '',
    clients: parseClientIds(props.filters.clients),
    statuses: props.filters.statuses ? (Array.isArray(props.filters.statuses) ? props.filters.statuses : [props.filters.statuses]) : [],
});

// Watch for external changes (URL updates)
watch(() => props.filters, (newFilters) => {
    localFilters.value.search = newFilters.search || '';
    localFilters.value.scope = newFilters.scope || 'current';
    localFilters.value.from_date = newFilters.from_date || '';
    localFilters.value.to_date = newFilters.to_date || '';
    localFilters.value.clients = parseClientIds(newFilters.clients);
    localFilters.value.statuses = newFilters.statuses ? (Array.isArray(newFilters.statuses) ? newFilters.statuses : [newFilters.statuses]) : [];
}, { deep: true });

const clientSearch = ref('');
const filteredClients = ref<Client[]>([]);

watch([clientSearch, () => props.clients], ([search]) => {
    if (!search) {
        filteredClients.value = props.clients.slice(0, 15);
        return;
    }
    const s = search.toLowerCase();
    filteredClients.value = props.clients.filter(c => 
        c.name.toLowerCase().includes(s) || 
        c.phone_number?.includes(s)
    ).slice(0, 15);
}, { immediate: true });

const selectedClientsNames = computed(() => {
    const selected = props.clients.filter(c => localFilters.value.clients.includes(c.id));
    if (selected.length === 0) return 'Klientki';
    if (selected.length === 1) return selected[0].name;
    return `Wybrano: ${selected.length}`;
});

const updateFilters = () => {
    router.get(admin.appointments.index(), {
        ...localFilters.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

// Debounced search
let searchTimeout: any;
watch(() => localFilters.value.search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(updateFilters, 300);
});

const toggleStatus = (status: string) => {
    const index = localFilters.value.statuses.indexOf(status);
    if (index > -1) {
        localFilters.value.statuses.splice(index, 1);
    } else {
        localFilters.value.statuses.push(status);
    }
    updateFilters();
};

const toggleClient = (clientId: number) => {
    const id = Number(clientId);
    const index = localFilters.value.clients.indexOf(id);
    if (index > -1) {
        localFilters.value.clients.splice(index, 1);
    } else {
        localFilters.value.clients.push(id);
    }
    updateFilters();
};

const clearFilters = () => {
    localFilters.value = {
        search: '',
        scope: 'current',
        from_date: '',
        to_date: '',
        clients: [],
        statuses: [],
    };
    updateFilters();
};

const setDateRange = (date: any) => {
    if (date?.from) localFilters.value.from_date = format(date.from, 'yyyy-MM-dd');
    if (date?.to) localFilters.value.to_date = format(date.to, 'yyyy-MM-dd');
    
    if (!date?.from) {
        localFilters.value.from_date = '';
        localFilters.value.to_date = '';
    }
    updateFilters();
};

const hasActiveFilters = computed(() => {
    return localFilters.value.search !== '' || 
           localFilters.value.scope !== 'current' || 
           localFilters.value.from_date !== '' || 
           localFilters.value.clients.length > 0 || 
           localFilters.value.statuses.length > 0;
});
</script>

<template>
    <div class="space-y-4 bg-muted/30 p-4 rounded-xl border border-border/50">
        <!-- Main Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="md:col-span-5 lg:col-span-6 relative">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                <Input
                    v-model="localFilters.search"
                    placeholder="Szukaj po klientce..."
                    class="pl-10 h-10 bg-background border-border/50 focus-visible:ring-primary"
                />
            </div>

            <!-- Scope -->
            <div class="md:col-span-3 lg:col-span-2 flex items-center bg-background rounded-md border border-border/50 p-1 h-10">
                <button
                    @click="localFilters.scope = 'current'; updateFilters()"
                    :class="[
                        'flex-1 px-2 py-1 text-xs font-medium rounded-sm transition-all',
                        localFilters.scope === 'current' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-muted text-muted-foreground'
                    ]"
                >
                    Aktualne
                </button>
                <button
                    @click="localFilters.scope = 'all'; updateFilters()"
                    :class="[
                        'flex-1 px-2 py-1 text-xs font-medium rounded-sm transition-all',
                        localFilters.scope === 'all' ? 'bg-primary text-primary-foreground shadow-sm' : 'hover:bg-muted text-muted-foreground'
                    ]"
                >
                    Wszystkie
                </button>
            </div>

            <!-- Date -->
            <div class="md:col-span-2 lg:col-span-2">
                <Popover>
                    <PopoverTrigger asChild>
                        <Button variant="outline" class="w-full h-10 px-3 justify-start font-normal bg-background border-border/50">
                            <CalendarIcon class="mr-2 h-4 w-4 shrink-0 text-muted-foreground" />
                            <span class="truncate text-xs">
                                <template v-if="localFilters.from_date">
                                    {{ format(new Date(localFilters.from_date), 'dd.MM') }} - 
                                    {{ localFilters.to_date ? format(new Date(localFilters.to_date), 'dd.MM') : '...' }}
                                </template>
                                <template v-else>Zakres dat</template>
                            </span>
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-auto p-0" align="end">
                        <Calendar
                            mode="range"
                            :selected="{ from: localFilters.from_date ? new Date(localFilters.from_date) : undefined, to: localFilters.to_date ? new Date(localFilters.to_date) : undefined }"
                            @update:selected="setDateRange"
                            initial-focus
                        />
                    </PopoverContent>
                </Popover>
            </div>

            <!-- Clients -->
            <div class="md:col-span-2 lg:col-span-2">
                <Popover>
                    <PopoverTrigger asChild>
                        <Button variant="outline" class="w-full h-10 px-3 justify-start bg-background border-border/50 overflow-hidden">
                            <Filter class="mr-2 h-4 w-4 shrink-0 text-muted-foreground" />
                            <span class="truncate text-xs font-medium">{{ selectedClientsNames }}</span>
                            <Badge v-if="localFilters.clients.length" variant="secondary" class="ml-auto px-1.5 py-0 h-5 text-[10px]">
                                {{ localFilters.clients.length }}
                            </Badge>
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-72 p-0" align="end">
                        <div class="p-3 border-b bg-muted/20">
                            <div class="relative">
                                <Search class="absolute left-2 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground" />
                                <Input v-model="clientSearch" placeholder="Szukaj klientki..." class="pl-8 h-8 text-xs" />
                            </div>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto p-1">
                            <div 
                                v-for="client in filteredClients" 
                                :key="client.id"
                                @click="toggleClient(client.id)"
                                class="flex items-center justify-between p-2.5 rounded-md hover:bg-muted cursor-pointer transition-colors group"
                            >
                                <div class="flex flex-col min-w-0">
                                    <span 
                                        class="text-sm font-medium truncate" 
                                        :class="[localFilters.clients.includes(client.id) ? 'text-primary' : 'text-foreground']"
                                    >
                                        {{ client.name }}
                                    </span>
                                    <span class="text-xs text-muted-foreground truncate">{{ client.phone_number }}</span>
                                </div>
                                <Check 
                                    v-if="localFilters.clients.includes(client.id)"
                                    class="h-4 w-4 text-primary shrink-0 ml-2" 
                                />
                            </div>
                            <div v-if="filteredClients.length === 0" class="p-6 text-center text-xs text-muted-foreground">
                                Nie znaleziono klientki
                            </div>
                        </div>
                    </PopoverContent>
                </Popover>
            </div>
        </div>

        <!-- Secondary Row: Statuses & Clear -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider mr-1">Status:</span>
                <button
                    v-for="status in statuses"
                    :key="status"
                    @click="toggleStatus(status)"
                    :class="[
                        'px-3 py-1 rounded-full text-xs font-medium transition-all border shadow-sm',
                        localFilters.statuses.includes(status)
                            ? 'bg-primary border-primary text-primary-foreground'
                            : 'bg-background border-border/50 text-muted-foreground hover:border-primary/50'
                    ]"
                >
                    {{ status }}
                </button>
            </div>

            <!-- Clear button - now using visibility to prevent layout jump -->
            <Button 
                variant="ghost" 
                size="sm"
                class="h-8 text-xs text-muted-foreground hover:text-destructive transition-colors"
                :class="[!hasActiveFilters && 'invisible pointer-events-none']"
                @click="clearFilters"
            >
                <FilterX class="mr-2 h-3.5 w-3.5" />
                Wyczyść wszystkie filtry
            </Button>
        </div>
    </div>
</template>
