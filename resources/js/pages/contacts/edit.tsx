import {Head} from "@inertiajs/react";
import ContactAddress from "@/components/contact-address";
import AppLayout from "@/layouts/app-layout";
import {dashboard} from "@/routes";
import contactRoutes from "@/routes/contacts";
import type {BreadcrumbItem, Contact, SelectOption} from "@/types";
import ContactForm from "./contact-form";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Dashboard",
        href: dashboard(),
    },
    {
        title: "Contacts",
        href: contactRoutes.index(),
    },
    {
        title: "Edit Contact",
        href: "#",
    },
];

type Props = {
    statuses: SelectOption[];
    contact: Contact;
    countries: SelectOption[];
    can: {
        create_address: boolean;
        update_address: boolean;
    };
};

export default function Edit({statuses, contact, countries, can}: Props) {
    const formProps = contactRoutes.update.form(contact.id);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Contact" />

            <div className="space-y-6 p-6 max-w-2xl">
                <div className="space-y-4">
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

                <ContactAddress
                    contact={contact}
                    countries={countries}
                    can={can}
                />
            </div>
        </AppLayout>
    );
}
