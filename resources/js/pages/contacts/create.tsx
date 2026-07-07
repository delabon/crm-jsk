import {Head} from '@inertiajs/react';
import {AlertCircleIcon} from "lucide-react";
import {Alert, AlertTitle} from "@/components/ui/alert";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import contactRoutes from '@/routes/contacts';
import type {BreadcrumbItem, SelectOption} from '@/types';
import ContactForm from "./contact-form";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Contacts',
        href: contactRoutes.index(),
    },
    {
        title: 'Create Contact',
        href: '#',
    },
];

type Props = {
    statuses: SelectOption[];
};

export default function Create({statuses}: Props) {
    const formProps = contactRoutes.store.form();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Contact" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Create contact
                </h1>

                <ContactForm
                    action={formProps.action}
                    method={formProps.method}
                    statuses={statuses}
                />
            </div>

            <div className="space-y-4 p-6 max-w-xl">
                <Alert className="w-full">
                    <AlertCircleIcon />
                    <AlertTitle>Address fields will be available after saving the contact.</AlertTitle>
                </Alert>
            </div>
        </AppLayout>
    );
}
