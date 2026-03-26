<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Client } from '@/types';
import { useClientForm } from './useClientForm';

const props = defineProps<{
    client?: Client;
    onSuccess?: () => void;
    redirectBack?: boolean;
}>();

const { form, submit } = useClientForm(props.client, props.onSuccess, props.redirectBack);
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6 max-w-xl">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label for="name">Imię i nazwisko</Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="np. Anna Kowalska"
                    autofocus
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="ig_name">Instagram (np. @nazwa)</Label>
                <Input
                    id="ig_name"
                    v-model="form.ig_name"
                    placeholder="@"
                />
                <InputError :message="form.errors.ig_name" />
            </div>

            <div class="grid gap-2">
                <Label for="phone_number">Numer telefonu</Label>
                <Input
                    id="phone_number"
                    v-model="form.phone_number"
                    placeholder="np. 123 456 789"
                />
                <InputError :message="form.errors.phone_number" />
            </div>

            <div class="grid gap-2">
                <Label for="note">Notatka</Label>
                <Textarea
                    id="note"
                    v-model="form.note"
                    placeholder="Dodatkowe informacje o kliencie..."
                    rows="4"
                />
                <InputError :message="form.errors.note" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <Button type="submit" :disabled="form.processing">
                {{ client ? 'Zapisz zmiany' : 'Dodaj klienta' }}
            </Button>
        </div>
    </form>
</template>
