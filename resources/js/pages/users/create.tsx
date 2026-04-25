import {Form, Head} from '@inertiajs/react';
import PasswordInput from "@/components/password-input";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {SelectWithItems} from "@/components/ui/select-with-items";
import {Spinner} from "@/components/ui/spinner";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import users from '@/routes/users';
import type {BreadcrumbItem, SelectOption} from '@/types';

type Props = {
    roles: SelectOption[];
};

export default function Create({roles}: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
        {
            title: 'Users',
            href: users.index(),
        },
        {
            title: 'Create User',
            href: users.create(),
        },
    ];
    const formProps = users.store.form();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create User" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Create user
                </h1>

                <Form {...formProps} className="w-full flex flex-col gap-4">
                    {({errors, processing}) => (
                        <>
                            <FormField label="First name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                                <Input
                                    id="first_name"
                                    name="first_name"
                                    placeholder="First name"
                                    aria-invalid={!!errors['first_name']}
                                />
                            </FormField>

                            <FormField label="Last name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                                <Input
                                    id="last_name"
                                    name="last_name"
                                    placeholder="Last name"
                                    aria-invalid={!!errors['last_name']}
                                />
                            </FormField>

                            <FormField label="Email" htmlFor="email" error={errors['email'] ?? null}>
                                <Input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Email"
                                    aria-invalid={!!errors['email']}
                                />
                            </FormField>

                            <FormField label="Role" htmlFor="role" error={errors['role'] ?? null}>
                                <SelectWithItems
                                    id="role"
                                    name="role"
                                    items={roles}
                                    placeholder="Select role"
                                    aria-invalid={!!errors['role']}
                                />
                            </FormField>

                            <FormField label="Password" htmlFor="password" error={errors['password'] ?? null}>
                                <PasswordInput
                                    id="password"
                                    required
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Password"
                                    aria-invalid={!!errors['password']}
                                />
                            </FormField>

                            <FormField label="Confirm password" htmlFor="password_confirmation" error={errors['password_confirmation'] ?? null}>
                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Confirm password"
                                    aria-invalid={!!errors['password_confirmation']}
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
            </div>
        </AppLayout>
    );
}
