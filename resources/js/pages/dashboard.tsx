import {Head, usePage} from '@inertiajs/react';
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
        accounts?: {
            all: string;
        };
    };
}

export default function Dashboard({metrics}: Props) {
    const {auth} = usePage().props;
    const {users: userMetrics, accounts: accountMetrics} = metrics;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <h1 className="font-bold">Hi {auth.user.first_name}</h1>

                {/* Users */}
                <div className="grid gap-4 lg:grid-cols-3">
                    {auth.user.permission_names?.includes('users.manage') &&
                        <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                            <div className="w-full p-6 flex flex-col gap-3">
                                <h3 className="text-xl font-bold">Users</h3>
                                <ul className="flex flex-col gap-2">
                                    <li><span className="font-semibold">Total Users:</span> {userMetrics?.all ?? '0'}</li>
                                    <li><span className="font-semibold">Super Admins:</span> {userMetrics?.super_admin ?? '0'}</li>
                                    <li><span className="font-semibold">Managers:</span> {userMetrics?.manager ?? '0'}</li>
                                    <li><span className="font-semibold">Sales Agents:</span> {userMetrics?.sales_agent ?? '0'}</li>
                                    <li><span className="font-semibold">Normal Users:</span> {userMetrics?.user ?? '0'}</li>
                                </ul>
                            </div>
                        </div>
                    }
                </div>

                {/* Accounts */}
                <div className="grid gap-4 lg:grid-cols-3">
                    {auth.user.permission_names?.includes('accounts.view-own') &&
                        <div className="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                            <div className="w-full p-6 flex flex-col gap-3">
                                <h3 className="text-xl font-bold">Accounts</h3>
                                <ul className="flex flex-col gap-2">
                                    <li><span className="font-semibold">Total Accounts:</span> {accountMetrics?.all ?? '0'}</li>
                                </ul>
                            </div>
                        </div>
                    }
                </div>
            </div>
        </AppLayout>
    );
}
