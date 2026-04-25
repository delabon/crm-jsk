import {Form, Head} from '@inertiajs/react';
import Heading from "@/components/heading";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {SelectWithItems} from "@/components/ui/select-with-items";
import {Spinner} from "@/components/ui/spinner";
import UpdatePassword from "@/components/update-password";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import UserPasswordRoute from "@/routes/user-password";
import users from '@/routes/users';
import type {BreadcrumbItem, SelectOption, User} from '@/types';

type Props = {
    user: User;
    roles: SelectOption[];
};

export default function UserForm({user, roles}: Props) {
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
            title: 'Edit User',
            href: users.edit(user.id),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit User" />

            <div className="space-y-6 p-6 max-w-xl">
                <Heading
                    variant="small"
                    title="Edit user"
                />

                <Form
                    {...users.update.form(user.id)}
                    className="w-full flex flex-col gap-4"
                >
                    {({errors, processing}) => (
                        <>
                            <FormField label="First name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                                <Input
                                    id="first_name"
                                    name="first_name"
                                    placeholder="First name"
                                    aria-invalid={!!errors['first_name']}
                                    defaultValue={user.first_name} />
                            </FormField>

                            <FormField label="Last name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                                <Input
                                    id="last_name"
                                    name="last_name"
                                    placeholder="Last name"
                                    aria-invalid={!!errors['last_name']}
                                    defaultValue={user.last_name} />
                            </FormField>

                            <FormField label="Email" htmlFor="email" error={errors['email'] ?? null}>
                                <Input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Email"
                                    aria-invalid={!!errors['email']}
                                    defaultValue={user.email}
                                />
                            </FormField>

                            <FormField label="Role" htmlFor="role" error={errors['role'] ?? null}>
                                <SelectWithItems
                                    id="role"
                                    name="role"
                                    items={roles}
                                    placeholder="Select role"
                                    aria-invalid={!!errors['role']}
                                    defaultValue={user.main_role ?? ''}
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

                <UpdatePassword action={UserPasswordRoute.update.form(user.id).action}/>
            </div>
        </AppLayout>
    );
}
