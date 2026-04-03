import { usePage } from "@inertiajs/react";
import { useEffect } from "react";
import { toast } from "sonner";

export function useFlashToast() {
    const { flash } = usePage<{ flash: { success?: string; error?: string } }>().props;

    useEffect(() => {
        const message = flash.success || flash.error;
        const type = flash.success ? 'success' : 'error';

        if (!message || message === '') {
            return;
        }

        toast[type](message);
    }, [flash]);
}

