import {Form} from "@inertiajs/react";
import Heading from "@/components/heading";
import InputError from "@/components/input-error";
import PasswordInput from "@/components/password-input";
import {Button} from "@/components/ui/button";
import {Label} from "@/components/ui/label";
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
            className="space-y-4 "
        >
            {({ errors, processing }) => (
                <>
                    <div className="grid gap-2">
                        <Label htmlFor="current_password">
                            Current password
                        </Label>

                        <PasswordInput
                            id="current_password"
                            name="current_password"
                            className="mt-1 block w-full"
                            autoComplete="current-password"
                            placeholder="Current password"
                        />

                        <InputError
                            message={errors.current_password}
                        />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password">
                            New password
                        </Label>

                        <PasswordInput
                            id="password"
                            name="password"
                            className="mt-1 block w-full"
                            autoComplete="new-password"
                            placeholder="New password"
                        />

                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">
                            Confirm password
                        </Label>

                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            className="mt-1 block w-full"
                            autoComplete="new-password"
                            placeholder="Confirm password"
                        />

                        <InputError
                            message={errors.password_confirmation}
                        />
                    </div>

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
