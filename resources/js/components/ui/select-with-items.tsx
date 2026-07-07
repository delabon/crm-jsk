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
import type {SelectOption} from "@/types";

type Props = {
    items: SelectOption[],
    placeholder?: string,
    className?: string,
    name: string,
    defaultValue?: string,
    value?: string | null,
    onValueChange?: (value: string) => void,
} & Omit<ComponentProps<typeof SelectTrigger>, "value" | "name" | "defaultValue" | "onValueChange">;

export function SelectWithItems({items, placeholder, name, defaultValue, value, onValueChange, ...props}: Props) {
    return <Select<string, false> name={name} defaultValue={defaultValue} value={value} items={items} onValueChange={onValueChange ? (val) => onValueChange((val as string) ?? "") : undefined}>
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
