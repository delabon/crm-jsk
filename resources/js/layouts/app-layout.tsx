import {Toaster} from "@/components/ui/sonner";
import {useFlashToast} from "@/hooks/use-flash-toast";
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import type { AppLayoutProps } from '@/types';

export default function AppLayout({ children, breadcrumbs, ...props }: AppLayoutProps) {
    useFlashToast();

    return <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
        <div>
            <Toaster position="top-right" closeButton/>
            {children}
        </div>
    </AppLayoutTemplate>;
};
