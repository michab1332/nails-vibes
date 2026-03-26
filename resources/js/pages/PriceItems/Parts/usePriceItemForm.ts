import { useForm } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import type { PriceItem } from '@/types';

export const usePriceItemForm = (priceItem?: PriceItem) => {
    const form = useForm({
        name: priceItem?.name ?? '',
        category: priceItem?.category ?? '',
        price_min: priceItem?.price_min ?? 0,
        price_max: priceItem?.price_max ?? null,
    });

    const submit = () => {
        if (priceItem) {
            form.put(admin.priceItems.update(priceItem).url);
        } else {
            form.post(admin.priceItems.store().url);
        }
    };

    return {
        form,
        submit,
    };
};
