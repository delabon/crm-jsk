import {Head} from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import AccountForm from "@/pages/accounts/account-form";
import {dashboard} from "@/routes";
import accountRoutes from '@/routes/accounts';
import type {BreadcrumbItem} from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Accounts',
        href: accountRoutes.index(),
    },
    {
        title: 'Create Account',
        href: '#',
    },
];

export default function Create() {
    const formProps = accountRoutes.store.form();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Account" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Create account
                </h1>

                <AccountForm action={formProps.action} method={formProps.method}/>
            </div>
        </AppLayout>
    );
}
