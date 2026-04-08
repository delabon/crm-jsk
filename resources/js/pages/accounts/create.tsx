import {Form, Head} from '@inertiajs/react';
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {Spinner} from "@/components/ui/spinner";
import AppLayout from '@/layouts/app-layout';
import {dashboard} from "@/routes";
import accountRoutes from '@/routes/accounts';
import type {BreadcrumbItem} from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
    {
        title: 'Accounts',
        href: accountRoutes.index(),
    },
    {
        title: 'Create Account',
        href: '#',
    },
];

export default function Create() {
    const formProps = accountRoutes.store.form();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Account" />

            <div className="space-y-4 p-6 max-w-xl">
                <h1 className="text-xl font-bold">
                    Create account
                </h1>

                <Form {...formProps} className="w-full flex flex-col gap-4">
                    {({errors, processing}) => (
                        <>
                            <FormField label="Name" htmlFor="name" error={errors['name'] ?? null}>
                                <Input
                                    id="name"
                                    name="name"
                                    placeholder="Name"
                                    aria-invalid={!!errors['name']}
                                />
                            </FormField>

                            <FormField label="Industry" htmlFor="industry" error={errors['industry'] ?? null}>
                                <Input
                                    id="industry"
                                    name="industry"
                                    placeholder="Industry"
                                    aria-invalid={!!errors['industry']}
                                />
                            </FormField>

                            <FormField label="Website" htmlFor="website" error={errors['website'] ?? null}>
                                <Input
                                    id="website"
                                    name="website"
                                    placeholder="Website"
                                    aria-invalid={!!errors['website']}
                                />
                            </FormField>

                            <FormField label="Phone" htmlFor="phone" error={errors['phone'] ?? null}>
                                <Input
                                    id="phone"
                                    name="phone"
                                    placeholder="Phone"
                                    aria-invalid={!!errors['phone']}
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
