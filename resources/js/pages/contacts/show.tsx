import { Head, Link } from '@inertiajs/react';
import {
    Building2Icon,
    Calendar,
    Edit,
    Mail,
    Phone,
    User,
} from 'lucide-react';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { useInitials } from '@/hooks/use-initials';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import accountRoutes from '@/routes/accounts';
import contactRoutes from '@/routes/contacts';
import type { BreadcrumbItem, Contact } from '@/types';

type Props = {
    contact: Contact;
    can: {
        update: boolean;
        delete: boolean;
    };
};

export default function Show({ contact, can }: Props) {
    const getInitials = useInitials();
    const owner = contact.owner;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard()
        },
        {
            title: 'Contacts',
            href: contactRoutes.index()
        },
        {
            title: `${contact.first_name} ${contact.last_name}`,
            href: contactRoutes.show(contact.id)
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${contact.first_name} ${contact.last_name}`} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div className="space-y-1">
                        <div className="flex items-center gap-3">
                            <h1 className="text-2xl font-bold tracking-tight">
                                {contact.first_name} {contact.last_name}
                            </h1>
                            <Badge variant="secondary">
                                {contact.status_label}
                            </Badge>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            Created on {contact.formatted_created_at}
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        {can.update && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={contactRoutes.edit(contact.id)}>
                                    <Edit className="mr-1 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                        )}
                    </div>
                </div>

                <Separator />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-4 w-4" />
                                    Contact Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Email
                                        </dt>
                                        <dd>
                                            {contact.email ? (
                                                <a
                                                    href={`mailto:${contact.email}`}
                                                    className="inline-flex items-center gap-1.5 text-sm text-primary hover:underline"
                                                >
                                                    <Mail className="h-3.5 w-3.5" />
                                                    {contact.email}
                                                </a>
                                            ) : (
                                                <span className="text-sm text-muted-foreground">—</span>
                                            )}
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Phone
                                        </dt>
                                        <dd className="flex items-center gap-1.5 text-sm">
                                            <Phone className="h-3.5 w-3.5 text-muted-foreground" />
                                            {contact.phone || '—'}
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Status
                                        </dt>
                                        <dd>
                                            <Badge variant="secondary">
                                                {contact.status_label}
                                            </Badge>
                                        </dd>
                                    </div>
                                    <div className="space-y-1">
                                        <dt className="text-sm font-medium text-muted-foreground">
                                            Created
                                        </dt>
                                        <dd className="flex items-center gap-1.5 text-sm">
                                            <Calendar className="h-3.5 w-3.5 text-muted-foreground" />
                                            {contact.formatted_created_at}
                                        </dd>
                                    </div>
                                </dl>
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

                        {contact.account && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Account</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-center gap-3">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                            <Building2Icon className="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <Link
                                                href={accountRoutes.show(contact.account.id).url}
                                                className="text-sm font-medium text-primary hover:underline"
                                            >
                                                {contact.account.name}
                                            </Link>
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
