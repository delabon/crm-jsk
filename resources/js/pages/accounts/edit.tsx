import {Head} from '@inertiajs/react';
import AccountAddresses from '@/components/account-addresses';
import AppLayout from '@/layouts/app-layout';
import AccountForm from "@/pages/accounts/account-form";
import {dashboard} from "@/routes";
import accountRoutes from '@/routes/accounts';
import type {Account, BreadcrumbItem, SelectOption} from '@/types';

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
    countries: SelectOption[];
    can: {
        create_address: boolean;
        update_address: boolean;
        delete_address: boolean;
    };
};

export default function Edit({account, countries, can}: Props) {
    const formProps = accountRoutes.update.form(account);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Account" />

            <div className="space-y-6 p-6 max-w-2xl">
                <div className="space-y-4">
                    <h1 className="text-xl font-bold">
                        Edit account
                    </h1>

                    <AccountForm action={formProps.action} method={formProps.method} account={account}/>
                </div>

                <AccountAddresses
                    account={account}
                    countries={countries}
                    can={can}
                />
            </div>
        </AppLayout>
    );
}
