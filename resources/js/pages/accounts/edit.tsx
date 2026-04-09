import {Head} from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import AccountForm from "@/pages/accounts/account-form";
import {dashboard} from "@/routes";
import accountRoutes from '@/routes/accounts';
import type {Account, BreadcrumbItem} from '@/types';

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
        title: 'Edit Account',
        href: '#',
    },
];

type Props = {
    account: Account;
};

export default function Edit({account}: Props) {
    const formProps = accountRoutes.update.form(account);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Account" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Edit account
                </h1>

                <AccountForm action={formProps.action} method={formProps.method} account={account}/>
            </div>
        </AppLayout>
    );
}
