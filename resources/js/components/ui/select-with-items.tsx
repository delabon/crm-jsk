import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import {ComponentProps} from "react";
import { cn } from "@/lib/utils"

export type SelectOption = {
    label: string,
    value: string,
};

type Props = {
    items: SelectOption[],
    placeholder?: string,
    className?: string,
    name: string,
    defaultValue?: string,
} & ComponentProps<typeof SelectTrigger>;

export function SelectWithItems({items, placeholder, name, defaultValue, ...props}: Props) {
    return <Select name={name} defaultValue={defaultValue}>
        <SelectTrigger {...props} className={cn("w-full", props.className)}>
            <SelectValue placeholder={placeholder}/>
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                {items.map((item) => (
                    <SelectItem key={item.value} value={item.value}>
                        {item.label}
                    </SelectItem>
                ))}
            </SelectGroup>
        </SelectContent>
    </Select>
}
