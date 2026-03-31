import {Head, Link, usePage} from '@inertiajs/react';
import {CollectionPagination} from "@/components/collection-pagination";
import DeleteButton from "@/components/delete-button";
import Filter from "@/components/filter";
import ListFilters from "@/components/list-filters";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {RadioWithItems} from "@/components/ui/radio-with-items";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import usersRoute from '@/routes/users';
import type {BreadcrumbItem, PaginatedCollection, RoleOption, User} from '@/types';
import ListSearch from "@/components/list-search";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Users',
        href: usersRoute.index(),
    },
];

const verifiedFilterItems = [
    {
        value: 'all',
        label: 'All'
    },
    {
        value: 'yes',
        label: 'Yes'
    },
    {
        value: 'no',
        label: 'No'
    },
];

type Filters = {
    role?: string;
    verified?: string;
};

type Props = {
    collection: PaginatedCollection<User>;
    roles: RoleOption[];
    filters: Filters;
    search?: string;
}

export default function Index({collection, roles, filters, search}: Props) {
    const {auth} = usePage().props;

    const canManageUser = (user: User) => {
        return auth.user.permission_names?.includes('users.manage')
            && (auth.user.id === user.id || !user.role_names?.includes('super_admin'))
    };

    const renderItems = () => {
        if (collection.data.length === 0) {
            return <div>No users yet.</div>
        }

        return <>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead className="w-25">ID</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Verified At</TableHead>
                        <TableHead>Registered At</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {collection.data.map((user: User) => (
                        <TableRow key={`users-${user.id}`}>
                            <TableCell className="font-medium">{user.id}</TableCell>
                            <TableCell>{user.first_name + ' ' + user.last_name}</TableCell>
                            <TableCell>{user.email}</TableCell>
                            <TableCell>{user.formatted_role}</TableCell>
                            <TableCell>{user.formatted_email_verified_at}</TableCell>
                            <TableCell>{user.formatted_created_at}</TableCell>
                            <TableCell>
                                <div className="inline-flex gap-2">
                                    {(
                                        canManageUser(user)
                                        &&
                                        <>
                                            <Button asChild variant="default">
                                                <Link href={usersRoute.edit(user.id).url}>Edit</Link>
                                            </Button>
                                            <DeleteButton
                                                {...usersRoute.destroy.form(user.id)}
                                            />
                                        </>
                                    )}
                                </div>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            <CollectionPagination collection={collection}/>
        </>
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />

            <div className="space-y-4 p-6">
                <div className="flex items-center justify-between gap-3 flex-wrap lg:flex-nowrap">
                    <h1 className="text-xl font-bold">Users</h1>
                    <div className="flex flex-wrap items-center gap-3">
                        <ListSearch action={usersRoute.index().url} initialSearch={search}/>
                        <ListFilters action={usersRoute.index().url}>
                            <Filter title="Verified">
                                <FormField>
                                    <RadioWithItems
                                        name="verified"
                                        defaultValue={filters.verified ?? 'all'}
                                        items={verifiedFilterItems}
                                    />
                                </FormField>
                            </Filter>

                            <Filter title="Role">
                                <FormField>
                                    <RadioWithItems
                                        name="role"
                                        defaultValue={filters.role ?? 'all'}
                                        items={roles}
                                    />
                                </FormField>
                            </Filter>
                        </ListFilters>
                        <Button
                            asChild
                        >
                            <Link href={usersRoute.create()}>
                                Create User
                            </Link>
                        </Button>
                    </div>
                </div>

                {renderItems()}
            </div>
        </AppLayout>
    );
}
