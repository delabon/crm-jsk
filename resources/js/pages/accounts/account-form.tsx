import {Form} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {Spinner} from "@/components/ui/spinner";
import { Textarea } from "@/components/ui/textarea"
import type {Account} from "@/types";

type Props = {
    action: string;
    method: "get" | "post" | "put" | "delete" | "patch" | undefined;
    account?: Account;
}

export default function AccountForm({action, method, account}: Props) {
    return <Form action={action} method={method} className="w-full flex flex-col gap-4">
        {({errors, processing}) => (
            <>
                <FormField label="Name" htmlFor="name" error={errors['name'] ?? null}>
                    <Input
                        id="name"
                        name="name"
                        placeholder="Name"
                        aria-invalid={!!errors['name']}
                        defaultValue={account?.name}
                    />
                </FormField>

                <FormField label="Description" htmlFor="description" error={errors['description'] ?? null}>
                    <Textarea
                        id="description"
                        name="description"
                        placeholder="Description"
                        aria-invalid={!!errors['description']}
                        defaultValue={account?.description}
                    />
                </FormField>

                <FormField label="Industry" htmlFor="industry" error={errors['industry'] ?? null}>
                    <Input
                        id="industry"
                        name="industry"
                        placeholder="Industry"
                        aria-invalid={!!errors['industry']}
                        defaultValue={account?.industry}
                    />
                </FormField>

                <FormField label="Website" htmlFor="website" error={errors['website'] ?? null}>
                    <Input
                        id="website"
                        name="website"
                        placeholder="Website"
                        aria-invalid={!!errors['website']}
                        defaultValue={account?.website}
                    />
                </FormField>

                <FormField label="Phone" htmlFor="phone" error={errors['phone'] ?? null}>
                    <Input
                        id="phone"
                        name="phone"
                        placeholder="Phone"
                        aria-invalid={!!errors['phone']}
                        defaultValue={account?.phone}
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
