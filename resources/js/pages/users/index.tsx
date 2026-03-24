import {Form, Head, Link} from '@inertiajs/react';
import {CollectionPagination} from "@/components/collection-pagination";
import {Button} from "@/components/ui/button";
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
import type {BreadcrumbItem, PaginatedCollection, User} from '@/types';

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

type UsersCollection = {
    collection: PaginatedCollection<User>;
}

export default function Index({collection}: UsersCollection) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users" />

            <div className="space-y-4 p-6">
                <div className="flex items-center justify-between gap-3 flex-wrap lg:flex-nowrap">
                    <h1 className="text-xl font-bold">Users</h1>
                    <Button
                        asChild
                    >
                        <Link href={usersRoute.create()}>
                            Create User
                        </Link>
                    </Button>
                </div>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-[100px]">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
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
                                <TableCell>{user.formatted_email_verified_at}</TableCell>
                                <TableCell>{user.formatted_created_at}</TableCell>
                                <TableCell>
                                    <div className="inline-flex gap-2">
                                        <Button asChild variant="default">
                                            <Link>Edit</Link>
                                        </Button>
                                        <Form
                                            {...usersRoute.destroy.form(user.id)}
                                            onBefore={() => confirm('Are you sure you to permanently delete this user (#' + user.id + ')?')}
                                        >
                                            <Button
                                                variant="destructive"
                                                type="submit"
                                                className="cursor-pointer"
                                            >
                                                Delete
                                            </Button>
                                        </Form>
                                    </div>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>

                <CollectionPagination collection={collection}/>
            </div>
        </AppLayout>
    );
}
