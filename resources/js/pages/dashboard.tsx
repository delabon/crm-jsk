import {Head, usePage} from '@inertiajs/react';
// import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

type Props = {
    metrics: {
        users?: {
            all: string;
            super_admin: string;
            manager: string;
            sales_agent: string;
            user: string;
        };
    };
}

export default function Dashboard({metrics}: Props) {
    const {auth} = usePage().props;
    const {users: userMetrics} = metrics;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <h1 className="font-bold">Hi {auth.user.first_name}</h1>
                <div className="grid gap-4 lg:grid-cols-3">
                    {auth.user.permission_names?.includes('users.manage') &&
                        <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                            <div className="w-full p-6 flex flex-col gap-3">
                                <h3 className="text-xl font-bold">Users</h3>
                                <ul className="flex flex-col gap-2">
                                    <li><span className="font-semibold">All Users:</span> {userMetrics?.all ?? ''}</li>
                                    <li><span className="font-semibold">Super Admins:</span> {userMetrics?.super_admin ?? ''}</li>
                                    <li><span className="font-semibold">Managers:</span> {userMetrics?.manager ?? ''}</li>
                                    <li><span className="font-semibold">Sales Agents:</span> {userMetrics?.sales_agent ?? ''}</li>
                                    <li><span className="font-semibold">Normal Users:</span> {userMetrics?.user ?? '0'}</li>
                                </ul>
                            </div>
                        </div>
                    }

                    {/*<div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">*/}
                    {/*    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />*/}
                    {/*</div>*/}
                    {/*<div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">*/}
                    {/*    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />*/}
                    {/*</div>*/}
                </div>
                {/*<div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">*/}
                {/*    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />*/}
                {/*</div>*/}
            </div>
        </AppLayout>
    );
}
