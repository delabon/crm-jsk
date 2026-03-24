import { usePage } from "@inertiajs/react";
import { useEffect, useRef } from "react";
import { toast } from "sonner";

export function useFlashToast() {
    const { flash } = usePage<{ flash: { success?: string; error?: string } }>().props;
    const lastSeen = useRef<string | null>(null);

    useEffect(() => {
        const message = flash.success || flash.error;
        const type = flash.success ? 'success' : 'error';

        if (message && lastSeen.current !== message) {
            toast[type](message);
            lastSeen.current = message;
        }

        if (!message) {
            lastSeen.current = null;
        }
    }, [flash]);
}

