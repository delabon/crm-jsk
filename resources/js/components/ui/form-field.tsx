import {PropsWithChildren} from "react";
import {Label} from "@/components/ui/label";
import InputError from "@/components/input-error";
import InputHelp from "@/components/input-help";

type Props = PropsWithChildren<{
    htmlFor?: string,
    label: string,
    error?: string,
    help?: string,
}>;

export function FormField({children, htmlFor, label, error, help}: Props) {
    return <div className="flex flex-col gap-2">
        <Label htmlFor={htmlFor}>
            {label}
        </Label>
        {children}
        <InputHelp message={help} />
        <InputError message={error} />
    </div>
}
