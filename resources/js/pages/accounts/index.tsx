import {Head, Link, usePage} from '@inertiajs/react';
import { CollectionPagination } from '@/components/collection-pagination';
import DeleteButton from '@/components/delete-button';
import ListSearch from '@/components/list-search';
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import accountRoute from '@/routes/accounts';
import type { BreadcrumbItem, PaginatedCollection, Account } from '@/types';
import type { User } from '@/types/auth';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Accounts',
        href: accountRoute.index(),
    },
];

type Props = {
    collection: PaginatedCollection<Account>;
    search?: string;
};

export default function Index({ collection, search }: Props) {
    const {user} = usePage().props.auth;

    const renderItems = (user: User) => {
        if (collection.data.length === 0) {
            return <div>Nothing here, come back later!</div>;
        }

        return (
            <>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-25">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Industry</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Website</TableHead>
                            <TableHead>Owner</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {collection.data.map((account: Account) => (
                            <TableRow key={`accounts-${account.id}`}>
                                <TableCell className="font-medium">
                                    {account.id}
                                </TableCell>
                                <TableCell>
                                    <Link
                                        href={accountRoute.show(account.id).url}
                                        className="font-medium text-primary hover:underline"
                                    >
                                        {account.name}
                                    </Link>
                                </TableCell>
                                <TableCell>{account.industry}</TableCell>
                                <TableCell>{account.phone}</TableCell>
                                <TableCell>{account.website}</TableCell>
                                <TableCell>
                                    {account.owner?.name ?? '—'}
                                </TableCell>
                                <TableCell>
                                    {account.formatted_created_at}
                                </TableCell>
                                <TableCell>
                                    <div className="inline-flex gap-2">
                                        <Button asChild variant="default">
                                            <Link
                                                href={
                                                    accountRoute.edit(
                                                        account.id,
                                                    ).url
                                                }
                                            >
                                                Edit
                                            </Link>
                                        </Button>
                                        {user.permission_names?.includes('accounts.delete') &&
                                            <DeleteButton
                                                {...accountRoute.destroy.form(
                                                    account.id,
                                                )}
                                            />
                                        }
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>

                <CollectionPagination collection={collection} />
            </>
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Accounts" />

            <div className="space-y-4 p-6">
                <div className="flex flex-wrap items-center justify-between gap-3 lg:flex-nowrap">
                    <h1 className="text-xl font-bold">Accounts</h1>
                    <div className="flex flex-wrap items-center gap-3">
                        <ListSearch initialSearch={search} />

                        <Button asChild>
                            <Link href={accountRoute.create()}>
                                Create Account
                            </Link>
                        </Button>
                    </div>
                </div>

                {renderItems(user)}
            </div>
        </AppLayout>
    );
}
