import { Form, Head, Link, usePage } from '@inertiajs/react';
import DeleteUser from '@/components/delete-user';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {FormField} from "@/components/ui/form-field";
import { Input } from '@/components/ui/input';
import {Spinner} from "@/components/ui/spinner";
import UpdatePassword from "@/components/update-password";
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/profile';
import UserProfileRoute from '@/routes/profile';
import UserPasswordRoute from '@/routes/user-password';
import { send } from '@/routes/verification';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit(),
    },
];

export default function Profile({
    mustVerifyEmail,
    status,
}: {
    mustVerifyEmail: boolean;
    status?: string;
}) {
    const { auth } = usePage().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Profile settings" />

            <h1 className="sr-only">Profile settings</h1>

            <SettingsLayout>
                <div className="space-y-6 max-w-xl">
                    <Heading
                        variant="small"
                        title="Profile information"
                        description="Update your name and email address"
                    />

                    <Form
                        {...UserProfileRoute.update.form()}
                        options={{
                            preserveScroll: true,
                        }}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <FormField label="First name" htmlFor="first_name" error={errors['first_name'] ?? null}>
                                    <Input
                                        id="first_name"
                                        name="first_name"
                                        placeholder="First name"
                                        aria-invalid={!!errors['first_name']}
                                        defaultValue={auth.user.first_name}
                                        required
                                        autoComplete="first_name"
                                    />
                                </FormField>

                                <FormField label="Last name" htmlFor="last_name" error={errors['last_name'] ?? null}>
                                    <Input
                                        id="last_name"
                                        name="last_name"
                                        placeholder="Last name"
                                        aria-invalid={!!errors['last_name']}
                                        defaultValue={auth.user.last_name}
                                        required
                                        autoComplete="last_name"
                                    />
                                </FormField>

                                <FormField label="Email address" htmlFor="email" error={errors['email'] ?? null}>
                                    <Input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Last name"
                                        aria-invalid={!!errors['email']}
                                        defaultValue={auth.user.email}
                                        required
                                        autoComplete="email"
                                    />
                                </FormField>

                                {mustVerifyEmail &&
                                    auth.user.email_verified_at === null && (
                                        <div>
                                            <p className="-mt-4 text-sm text-muted-foreground">
                                                Your email address is
                                                unverified.{' '}
                                                <Link
                                                    href={send()}
                                                    as="button"
                                                    className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                                >
                                                    Click here to resend the
                                                    verification email.
                                                </Link>
                                            </p>

                                            {status ===
                                                'verification-link-sent' && (
                                                <div className="mt-2 text-sm font-medium text-green-600">
                                                    A new verification link has
                                                    been sent to your email
                                                    address.
                                                </div>
                                            )}
                                        </div>
                                    )}

                                <div className="flex items-center gap-4">
                                    <Button
                                        disabled={processing}
                                        className="cursor-pointer"
                                        data-test="update-profile-button"
                                    >
                                        {processing && <Spinner data-icon="inline-start" />}
                                        {processing ? 'Saving' : 'Save'}
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>

                <UpdatePassword action={UserPasswordRoute.update.form(auth.user.id).action}/>
                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
