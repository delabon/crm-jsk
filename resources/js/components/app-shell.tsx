import { usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { SidebarProvider } from '@/components/ui/sidebar';
import {Toaster} from "@/components/ui/sonner";
import {useFlashToast} from "@/hooks/use-flash-toast";
import type { AppVariant } from '@/types';

type Props = {
    children: ReactNode;
    variant?: AppVariant;
};

export function AppShell({ children, variant = 'sidebar' }: Props) {
    const isOpen = usePage().props.sidebarOpen;
    useFlashToast();

    if (variant === 'header') {
        return (
            <div className="flex min-h-screen w-full flex-col">{children}</div>
        );
    }

    return <>
        <Toaster position="top-right" closeButton />
        <SidebarProvider defaultOpen={isOpen}>{children}</SidebarProvider>
    </>;
}
