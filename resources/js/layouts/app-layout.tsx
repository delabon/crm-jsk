import {usePage} from "@inertiajs/react";
import {useEffect, useRef} from "react";
import {toast} from "sonner";
import {Toaster} from "@/components/ui/sonner";
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import type { AppLayoutProps } from '@/types';

const FlashToast = () => {
    // Specify your PageProps here or use a global type
    const { flash } = usePage<{ flash: { success?: string; error?: string } }>().props;
    const lastSeen = useRef<string | null>(null);

    useEffect(() => {
        const message = flash.success || flash.error;
        const type = flash.success ? 'success' : 'error';

        // Only show if there's a message AND it's not the exact same as the last one
        if (message && lastSeen.current !== message) {
            toast[type](message);
            lastSeen.current = message;
        }

        // Reset the tracker if the backend clears the flash
        if (!message) {
            lastSeen.current = null;
        }
    }, [flash]);

    return null;
}

export default function AppLayout({ children, breadcrumbs, ...props }: AppLayoutProps) {
    return <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        <div>
            <Toaster position="bottom-right" closeButton/>
            <FlashToast/>
            {children}
        </div>
    </AppLayoutTemplate>;
};
