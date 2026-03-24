import {Form, Head} from '@inertiajs/react';
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import users from '@/routes/users';
import type {BreadcrumbItem} from '@/types';

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
        title: 'Create',
        href: users.create(),
    },
];

export default function Create() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create User" />

            <div className="space-y-4 p-6">
                <h1 className="text-xl font-bold">Create User</h1>

                <Form {...users.store.form()} className="max-w-full w-100 flex flex-col gap-3">
                    {({errors, processing}) => (
                        <>
                            <div className="flex gap-3">
                                <FormField label="First Name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                                    <Input id="first_name" name="first_name" aria-invalid={!!errors['first_name']} />
                                </FormField>

                                <FormField label="Last Name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                                    <Input id="last_name" name="last_name" aria-invalid={!!errors['last_name']} />
                                </FormField>
                            </div>

                            <FormField label="Email" htmlFor="email" error={errors['email'] ?? null}>
                                <Input type="email" id="email" name="email" aria-invalid={!!errors['email']} />
                            </FormField>

                            <FormField label="Password" htmlFor="password" error={errors['password'] ?? null}>
                                <Input type="password" id="password" name="password" aria-invalid={!!errors['password']} />
                            </FormField>

                            <FormField label="Confirm Password" htmlFor="password_confirmation" error={errors['password_confirmation'] ?? null}>
                                <Input type="password" id="password_confirmation" name="password_confirmation" aria-invalid={!!errors['password_confirmation']} />
                            </FormField>

                            <div>
                                <Button
                                    disabled={processing}
                                >
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}
