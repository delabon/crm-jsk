import { Head, Link } from '@inertiajs/react';
import {
    Building2,
    Globe,
    Phone,
    Calendar,
    Users,
    DollarSign,
    Edit,
    ExternalLink,
    Mail,
    User,
} from 'lucide-react';
import DeleteButton from '@/components/delete-button';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useInitials } from '@/hooks/use-initials';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import accountRoute from '@/routes/accounts';
import contactRoute from '@/routes/contacts';
import type { Account, BreadcrumbItem, Contact } from '@/types';

type Props = {
    account: Account;
    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function Show({ account, can }: Props) {
    const getInitials = useInitials();
    const owner = account.owner;

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Accounts', href: accountRoute.index() },
        { title: account.name, href: accountRoute.show(account.id) },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={account.name} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div className="space-y-1">
                        <div className="flex items-center gap-3">
                            <h1 className="text-2xl font-bold tracking-tight">
                                {account.name}
                            </h1>
                            <Badge variant="secondary">
                                {account.industry}
                            </Badge>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            Created on {account.formatted_created_at}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        {can.update && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={accountRoute.edit(account.id)}>
                                    <Edit className="mr-1 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                        )}
                        {can.delete && (
                            <DeleteButton
                                size="sm"
                                {...accountRoute.destroy.form(account.id)}
                            />
                        )}
                    </div>
                </div>

                <Separator />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="h-4 w-4" />
                                    Account Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Industry
                                        </dt>
                                        <dd className="text-sm">
                                            {account.industry}
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Phone
                                        </dt>
                                        <dd className="flex items-center gap-1.5 text-sm">
                                            <Phone className="h-3.5 w-3.5 text-muted-foreground" />
                                            {account.phone}
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Website
                                        </dt>
                                        <dd>
                                            <a
                                                href={account.website}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1.5 text-sm text-primary hover:underline"
                                            >
                                                <Globe className="h-3.5 w-3.5" />
                                                {account.website.replace(
                                                    /^https?:\/\//,
                                                    '',
                                                )}
                                                <ExternalLink className="h-3 w-3" />
                                            </a>
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Created
                                        </dt>
                                        <dd className="flex items-center gap-1.5 text-sm">
                                            <Calendar className="h-3.5 w-3.5 text-muted-foreground" />
                                            {account.formatted_created_at}
                                        </dd>
                                    </div>
                                </dl>

                                {account.description && (
                                    <>
                                        <Separator className="my-4" />
                                        <div className="space-y-1.5">
                                            <dt className="text-sm font-medium text-muted-foreground">
                                                Description
                                            </dt>
                                            <dd className="text-sm leading-relaxed whitespace-pre-wrap">
                                                {account.description}
                                            </dd>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Users className="h-4 w-4" />
                                    Related Contacts
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                {account.contacts && account.contacts.length > 0 ? (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Name</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead>Email</TableHead>
                                                <TableHead>Phone</TableHead>
                                                <TableHead>Owner</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {account.contacts.map((contact: Contact) => (
                                                <TableRow key={contact.id}>
                                                    <TableCell>
                                                        <Link
                                                            href={contactRoute.show(contact.id).url}
                                                            className="font-medium text-primary hover:underline"
                                                        >
                                                            {contact.first_name} {contact.last_name}
                                                        </Link>
                                                    </TableCell>
                                                    <TableCell>
                                                        <Badge variant="secondary">
                                                            {contact.status_label}
                                                        </Badge>
                                                    </TableCell>
                                                    <TableCell>
                                                        {contact.email ? (
                                                            <span className="inline-flex items-center gap-1.5 text-sm">
                                                                <Mail className="h-3.5 w-3.5 text-muted-foreground" />
                                                                {contact.email}
                                                            </span>
                                                        ) : '—'}
                                                    </TableCell>
                                                    <TableCell>
                                                        {contact.phone || '—'}
                                                    </TableCell>
                                                    <TableCell>
                                                        {contact.owner?.name ?? '—'}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                ) : (
                                    <div className="flex flex-col items-center justify-center py-8 text-center">
                                        <div className="mb-3 rounded-full bg-muted p-3">
                                            <Users className="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <p className="text-sm font-medium">
                                            No contacts yet
                                        </p>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            Contacts associated with this account
                                            will appear here.
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <DollarSign className="h-4 w-4" />
                                    Related Deals
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex flex-col items-center justify-center py-8 text-center">
                                    <div className="mb-3 rounded-full bg-muted p-3">
                                        <DollarSign className="h-6 w-6 text-muted-foreground" />
                                    </div>
                                    <p className="text-sm font-medium">
                                        No deals yet
                                    </p>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        Deals linked to this account's contacts
                                        will appear here.
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        {owner && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Owner</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-center gap-3">
                                        <Avatar className="h-10 w-10">
                                            <AvatarFallback className="bg-neutral-200 text-sm text-black dark:bg-neutral-700 dark:text-white">
                                                {getInitials(owner.name ?? '')}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p className="text-sm font-medium">
                                                {owner.name}
                                            </p>
                                            {owner.formatted_role && (
                                                <p className="text-xs text-muted-foreground">
                                                    {owner.formatted_role}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
