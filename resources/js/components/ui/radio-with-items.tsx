import {RadioGroup, RadioGroupItem} from "@/components/ui/radio-group";
import {Label} from "@/components/ui/label";

export type RadioOption = {
    label: string,
    value: string,
};

type Props = {
    items: RadioOption[],
    name?: string,
    className?: string,
    defaultValue?: string,
};

export function RadioWithItems({items, name, className, defaultValue}: Props) {
    return <RadioGroup defaultValue={defaultValue ? defaultValue : ''} name={name} className="w-fit">
        {items.map(function (item) {
            const itemId = `radio-item-${item.value}`;

            return <div className="flex items-center gap-3 cursor-pointer" key={itemId}>
                <RadioGroupItem value={item.value} id={itemId} />
                <Label htmlFor={itemId} className="cursor-pointer">{item.label}</Label>
            </div>
        })}
    </RadioGroup>
}
