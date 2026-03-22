<template>
    <Head title="Inspiracje" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-2 sm:gap-6 sm:p-4">
            <div class="px-2 pt-2 md:px-0 md:pt-0">
                <h1 class="text-xl font-bold tracking-tight sm:text-2xl">Wiosenne inspiracje</h1>
                <p class="text-xs text-muted-foreground sm:text-sm">Najświeższe trendy "Spring Mani" prosto z Pinteresta.</p>
            </div>

            <!-- Masonry Grid (Pinterest Mobile Style) -->
            <div class="columns-2 gap-3 space-y-3 sm:columns-3 md:columns-4 lg:columns-5 xl:columns-6 sm:gap-4 sm:space-y-4">
                <div 
                    v-for="pin in inspirations" 
                    :key="pin.id"
                    class="break-inside-avoid rounded-lg overflow-hidden bg-muted/30 shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
                    @click="openPreview(pin)"
                >
                    <div class="relative overflow-hidden aspect-auto">
                        <img 
                            :src="pin.thumbnail || pin.image" 
                            :alt="pin.title"
                            class="w-full object-cover group-hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                            referrerpolicy="no-referrer"
                        />
                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <Search class="text-white h-6 w-6" />
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="inspirations.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
                <div class="rounded-full bg-muted p-6 mb-4">
                    <ImageOff class="h-10 w-10 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-semibold">Brak inspiracji</h3>
                <p class="text-muted-foreground">Nie udało się pobrać zdjęć. Odśwież stronę za chwilę.</p>
            </div>
        </div>

        <!-- Preview Modal -->
        <Dialog :open="!!selectedPin" @update:open="selectedPin = null">
            <DialogContent class="max-w-3xl p-0 overflow-hidden border-none bg-transparent shadow-none sm:max-w-2xl">
                <div class="relative flex flex-col items-center max-h-screen p-4 sm:p-6">
                    <div class="relative w-full flex flex-col items-center bg-black/5 backdrop-blur-sm rounded-2xl overflow-hidden">
                        <img 
                            v-if="selectedPin"
                            :src="selectedPin.image" 
                            :alt="selectedPin.title"
                            class="max-h-[70vh] w-full object-contain"
                            referrerpolicy="no-referrer"
                        />
                        
                        <div v-if="selectedPin" class="w-full p-4 bg-background/95 backdrop-blur border-t text-center">
                            <h3 class="text-base font-semibold mb-3">{{ selectedPin.title !== 'Pinterest' ? selectedPin.title : 'Stylizacja' }}</h3>
                            <div class="flex items-center justify-center gap-3">
                                <Button variant="default" size="sm" as-child>
                                    <a :href="selectedPin.link" target="_blank" rel="noopener noreferrer">
                                        Zobacz na Pinterest
                                    </a>
                                </Button>
                                <Button variant="outline" size="sm" @click="selectedPin = null">
                                    Zamknij
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Search, ImageOff, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import admin from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    inspirations: Array<{
        id: string;
        title: string;
        link: string;
        image: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inspiracje',
        href: admin.inspirations.index().url,
    },
];

const selectedPin = ref<any>(null);

const openPreview = (pin: any) => {
    selectedPin.value = pin;
};

</script>
