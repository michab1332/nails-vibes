import { useForm } from '@inertiajs/vue3';
import type { Client } from '@/types';
import admin from '@/routes/admin';

export const useClientForm = (client?: Client) => {
    const form = useForm({
        name: client?.name ?? '',
        ig_name: client?.ig_name ?? '',
        phone_number: client?.phone_number ?? '',
        note: client?.note ?? '',
    });

    const submit = () => {
        if (client) {
            form.put(admin.clients.update(client).url);
        } else {
            form.post(admin.clients.store().url);
        }
    };

    return {
        form,
        submit,
    };
};
