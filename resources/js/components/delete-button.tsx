import {Form} from "@inertiajs/react";
import {Button} from "@/components/ui/button";
import {Spinner} from "@/components/ui/spinner";

type Props = {
    action: string;
    method: "get" | "post" | "put" | "patch" | "delete" | undefined;
    message?: string;
}

export default function DeleteButton ({action, method, message}: Props) {
    return <Form
        action={action}
        method={method}
        onBefore={() => confirm(message ?? 'Are you sure you want to permanently delete this?')}
    >
        {({processing}) => (
            <Button
                variant="destructive"
                type="submit"
                disabled={processing}
            >
                {processing && <Spinner data-icon="inline-start" />}
                {processing ? 'Deleting' : 'Delete'}
            </Button>
        )}
    </Form>
}
