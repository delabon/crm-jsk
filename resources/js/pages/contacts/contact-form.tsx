import {Form} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Chips} from "@/components/ui/chips";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {SelectWithItems} from "@/components/ui/select-with-items";
import {Spinner} from "@/components/ui/spinner";
import { search as searchAccountsApiRoute} from '@/routes/api/private/v1/accounts';
import type {Contact, SelectOption} from "@/types";

type Props = {
    action: string;
    method: "get" | "post" | "put" | "delete" | "patch" | undefined;
    contact?: Contact;
    statuses: SelectOption[];
}

export default function ContactForm({action, method, contact, statuses}: Props) {
    return <Form action={action} method={method} className="w-full flex flex-col gap-4">
        {({errors, processing}) => (
            <>
                <FormField label="First name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                    <Input
                        id="first_name"
                        name="first_name"
                        placeholder="First name"
                        aria-invalid={!!errors['first_name']}
                        defaultValue={contact?.first_name}
                    />
                </FormField>

                <FormField label="Last name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                    <Input
                        id="last_name"
                        name="last_name"
                        placeholder="Last name"
                        aria-invalid={!!errors['last_name']}
                        defaultValue={contact?.last_name}
                    />
                </FormField>

                <FormField label="Phone" htmlFor="phone" error={errors['phone'] ?? null}>
                    <Input
                        id="phone"
                        name="phone"
                        placeholder="Phone"
                        aria-invalid={!!errors['phone']}
                        defaultValue={contact?.phone}
                    />
                </FormField>

                <FormField label="Email" htmlFor="email" error={errors['email'] ?? null}>
                    <Input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Email"
                        aria-invalid={!!errors['email']}
                        defaultValue={contact?.email}
                    />
                </FormField>

                <FormField label="Status" htmlFor="status" error={errors['status'] ?? null}>
                    <SelectWithItems
                        id="status"
                        name="status"
                        items={statuses}
                        placeholder="Select status"
                        aria-invalid={!!errors['status']}
                        defaultValue={contact?.status ?? ''}
                    />
                </FormField>

                <FormField label="Account" htmlFor="account_id" error={errors['account_id'] ?? null}>
                    <Chips
                        id="account_id"
                        name="account_id"
                        endpoint="/api/private/v1/accounts/search"
                        placeholder="Search accounts..."
                        defaultValue={contact?.account ? { value: String(contact.account.id), label: contact.account.name } : undefined}
                    />
                </FormField>

                <div>
                    <Button
                        disabled={processing}
                        className="cursor-pointer"
                    >
                        {processing && <Spinner data-icon="inline-start" />}
                        {processing ? 'Saving' : 'Save'}
                    </Button>
                </div>
            </>
        )}
    </Form>
}
