import {Form, Head} from '@inertiajs/react';
import PasswordInput from "@/components/password-input";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue} from "@/components/ui/select";
import {Spinner} from "@/components/ui/spinner";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import users from '@/routes/users';
import type {BreadcrumbItem, RoleOption, User} from '@/types';
import {SelectWithItems} from "@/components/ui/select-with-items";

type Props = {
    user: User;
    roles: RoleOption[];
};

export default function UserForm({user, roles}: Props) {
    const actionTitle = user?.id ? 'Edit' : 'Create';
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
            title: actionTitle,
            href: user?.id ? users.edit(user.id) : users.create() ,
        },
    ];
    const formProps = user?.id
        ? users.update.form(user.id)
        : users.store.form();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${actionTitle} User`} />

            <div className="space-y-4 p-6">
                <h1 className="text-xl font-bold">
                    {`${actionTitle} User`}
                </h1>

                <Form {...formProps} className="max-w-full w-100 flex flex-col gap-4">
                    {({errors, processing}) => (
                        <>
                            <FormField label="First Name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                                <Input
                                    id="first_name"
                                    name="first_name"
                                    placeholder="First name"
                                    aria-invalid={!!errors['first_name']}
                                    defaultValue={user?.id ? user.first_name : ''} />
                            </FormField>

                            <FormField label="Last Name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                                <Input
                                    id="last_name"
                                    name="last_name"
                                    placeholder="Last name"
                                    aria-invalid={!!errors['last_name']}
                                    defaultValue={user?.id ? user.last_name : ''} />
                            </FormField>

                            <FormField label="Email" htmlFor="email" error={errors['email'] ?? null}>
                                <Input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Email"
                                    aria-invalid={!!errors['email']}
                                    defaultValue={user?.id ? user.email : ''}
                                />
                            </FormField>

                            <FormField label="Role" htmlFor="role" error={errors['role'] ?? null}>
                                <SelectWithItems
                                    id="role"
                                    name="role"
                                    items={roles}
                                    placeholder="Select role"
                                    aria-invalid={!!errors['role']}
                                    defaultValue={user?.id ? user.main_role ?? '' : ''}
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

                            <FormField label="Confirm Password" htmlFor="password_confirmation" error={errors['password_confirmation'] ?? null}>
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
