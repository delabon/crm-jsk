import {Head} from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import contactRoutes from '@/routes/contacts';
import type {BreadcrumbItem, Contact, SelectOption} from '@/types';
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
        title: 'Edit Contact',
        href: '#',
    },
];

type Props = {
    statuses: SelectOption[];
    contact: Contact;
};

export default function Create({statuses, contact}: Props) {
    const formProps = contactRoutes.update.form(contact.id);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Contact" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Edit contact
                </h1>

                <ContactForm
                    action={formProps.action}
                    method={formProps.method}
                    statuses={statuses}
                    contact={contact}
                />
            </div>
        </AppLayout>
    );
}
