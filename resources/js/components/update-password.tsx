import {Form} from "@inertiajs/react";
import Heading from "@/components/heading";
import PasswordInput from "@/components/password-input";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Spinner} from "@/components/ui/spinner";

type Props = {
    action: string;
}

export default function UpdatePassword({action}: Props) {
    return <div className="space-y-6 max-w-xl">
        <Heading
            variant="small"
            title="Update password"
            description="Ensure your account is using a long, random password to stay secure"
        />

        <Form
            action={action}
            method="PUT"
            options={{
                preserveScroll: true,
            }}
            resetOnError={[
                'password',
                'password_confirmation',
                'current_password',
            ]}
            resetOnSuccess
            className="w-full flex flex-col gap-4"
        >
            {({ errors, processing }) => (
                <>
                    <FormField label="Current password" htmlFor="current_password" error={errors['current_password'] ?? null}>
                        <PasswordInput
                            id="current_password"
                            name="current_password"
                            autoComplete="current-password"
                            placeholder="Current password"
                            aria-invalid={!!errors['current_password']}
                        />
                    </FormField>

                    <FormField label="New password" htmlFor="password" error={errors['password'] ?? null}>
                        <PasswordInput
                            id="password"
                            name="password"
                            autoComplete="new-password"
                            placeholder="New password"
                            aria-invalid={!!errors['password']}
                        />
                    </FormField>

                    <FormField label="Confirm password" htmlFor="password_confirmation" error={errors['password_confirmation'] ?? null}>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            autoComplete="new-password"
                            placeholder="Confirm password"
                            aria-invalid={!!errors['password_confirmation']}
                        />
                    </FormField>

                    <div className="flex items-center gap-4">
                        <Button
                            disabled={processing}
                            className="cursor-pointer"
                            data-test="update-password-button"
                        >
                            {processing && <Spinner data-icon="inline-start" />}
                            {processing ? 'Saving' : 'Save'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    </div>
}
