import { useForm } from '@inertiajs/vue3';
import admin from '@/routes/admin';
import type { Client } from '@/types';

export const useClientForm = (client?: Client, onSuccess?: () => void, redirectBack: boolean = false) => {
    const form = useForm({
        name: client?.name ?? '',
        ig_name: client?.ig_name ?? '',
        phone_number: client?.phone_number ?? '',
        note: client?.note ?? '',
    });

    const submit = () => {
        const options = {
            onSuccess: () => {
                if (onSuccess) {
                    onSuccess();
                }
            },
        };

        const getUrl = (url: string) => {
            if (redirectBack) {
                return `${url}?redirect=back`;
            }
            return url;
        };

        if (client) {
            form.put(getUrl(admin.clients.update(client).url), options);
        } else {
            form.post(getUrl(admin.clients.store().url), options);
        }
    };

    return {
        form,
        submit,
    };
};
