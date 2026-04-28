import {Head, Link, usePage} from '@inertiajs/react';
import { CollectionPagination } from '@/components/collection-pagination';
import Filter from "@/components/filter";
import ListFilters from "@/components/list-filters";
import ListSearch from '@/components/list-search';
import { Button } from '@/components/ui/button';
import {FormField} from "@/components/ui/form-field";
import {RadioWithItems} from "@/components/ui/radio-with-items";
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
import contactRoute from '@/routes/contacts';
import type {BreadcrumbItem, PaginatedCollection, Contact, SelectOption} from '@/types';
import type { User } from '@/types/auth';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Contacts',
        href: contactRoute.index(),
    },
];

type Filters = {
    status?: string;
};

type Props = {
    collection: PaginatedCollection<Contact>;
    search?: string;
    statuses: SelectOption[];
    filters: Filters;
};

export default function Index({ collection, search, filters, statuses }: Props) {
    const {user} = usePage().props.auth;

    const renderItems = (user: User) => {
        if (collection.data.length === 0) {
            return <div>Nothing here, add yours!</div>;
        }

        return (
            <>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead className="w-25">ID</TableHead>
                            <TableHead>Name</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Phone</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Owner</TableHead>
                            <TableHead>Created At</TableHead>
                            <TableHead>Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {collection.data.map((contact: Contact) => (
                            <TableRow key={`contacts-${contact.id}`}>
                                <TableCell className="font-medium">
                                    {contact.id}
                                </TableCell>
                                <TableCell>
                                    <Link
                                        // href={contactRoute.show(contact.id).url}
                                        className="font-medium text-primary hover:underline"
                                    >
                                        {contact.first_name} {contact.last_name}
                                    </Link>
                                </TableCell>
                                <TableCell>{contact.status_label}</TableCell>
                                <TableCell>{contact.phone}</TableCell>
                                <TableCell>{contact.email}</TableCell>
                                <TableCell>
                                    {contact.owner?.name ?? '—'}
                                </TableCell>
                                <TableCell>
                                    {contact.formatted_created_at}
                                </TableCell>
                                <TableCell>
                                    <div className="inline-flex gap-2">
                                        <Button asChild variant="default">
                                            <Link
                                                href={
                                                    contactRoute.edit(
                                                        contact.id,
                                                    ).url
                                                }
                                            >
                                                Edit
                                            </Link>
                                        </Button>
                                        {user.permission_names?.includes('contacts.delete') &&
                                            <>Delete Button</>
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
            <Head title="Contacts" />

            <div className="space-y-4 p-6">
                <div className="flex flex-wrap items-center justify-between gap-3 lg:flex-nowrap">
                    <h1 className="text-xl font-bold">Contacts</h1>
                    <div className="flex flex-wrap items-center gap-3">
                        <ListSearch initialSearch={search} />
                        <ListFilters action={contactRoute.index().url}>
                            <Filter title="Status">
                                <FormField>
                                    <RadioWithItems
                                        name="status"
                                        defaultValue={filters.status ?? 'all'}
                                        items={statuses}
                                    />
                                </FormField>
                            </Filter>
                        </ListFilters>
                        <Button asChild>
                            <Link href={contactRoute.create().url}>
                                Create Contact
                            </Link>
                        </Button>
                    </div>
                </div>

                {renderItems(user)}
            </div>
        </AppLayout>
    );
}
