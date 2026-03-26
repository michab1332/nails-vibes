<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { PriceItem } from '@/types';
import { usePriceItemForm } from './usePriceItemForm';

const props = defineProps<{
    priceItem?: PriceItem;
    categories: { name: string; value: string }[] | string[];
}>();

const { form, submit } = usePriceItemForm(props.priceItem);

// Normalize categories to { label, value }
const normalizedCategories = Array.isArray(props.categories)
    ? props.categories.map((c: any) => {
          if (typeof c === 'object' && c.name && c.value) {
return { label: c.name, value: c.value };
}

          if (typeof c === 'object' && c.label && c.value) {
return c;
}

          return { label: c, value: c };
      })
    : [];

</script>

<template>
    <form @submit.prevent="submit" class="space-y-6 max-w-xl">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label for="name">Nazwa usługi</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="np. Manicure Hybrydowy"
                    :error="!!form.errors.name"
                    autofocus
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="category">Kategoria</Label>
                <Select v-model="form.category">
                    <SelectTrigger id="category">
                        <SelectValue placeholder="Wybierz kategorię" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="category in normalizedCategories"
                            :key="category.value"
                            :value="category.value"
                        >
                            {{ category.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.category" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="price_min">Cena minimalna (zł)</Label>
                    <Input
                        id="price_min"
                        type="number"
                        step="0.01"
                        v-model.number="form.price_min"
                    />
                    <InputError :message="form.errors.price_min" />
                </div>

                <div class="grid gap-2">
                    <Label for="price_max">Cena maksymalna (opcjonalnie)</Label>
                    <Input
                        id="price_max"
                        type="number"
                        step="0.01"
                        v-model.number="form.price_max"
                    />
                    <InputError :message="form.errors.price_max" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" :disabled="form.processing">
                {{ priceItem ? 'Zapisz zmiany' : 'Dodaj usługę' }}
            </Button>
        </div>
    </form>
</template>
