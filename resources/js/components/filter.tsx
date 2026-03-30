import {ChevronDownIcon, ChevronUpIcon} from "lucide-react";
import type {PropsWithChildren} from "react";
import {useState} from "react";
import {cn} from "@/lib/utils";

type Props = PropsWithChildren<{
    title: string;
}>;

export default function Filter({children, title}: Props) {
    const [isVisible, setIsVisible] = useState(false);
    const toggleFilter = () => {
        setIsVisible(!isVisible);
    };

    return <div className="dashboard-list-filter flex flex-col gap-3 border-b border-gray-200 pb-3">
        <div onClick={toggleFilter} className="cursor-pointer hover:opacity-80 text-sm font-semibold flex items-center justify-between gap-3">
            <span>{title}</span>
            <ChevronDownIcon size={16} className={isVisible ? 'hidden' : 'inline-flex'}/>
            <ChevronUpIcon size={16} className={isVisible ? 'inline-flex' : 'hidden'}/>
        </div>
        <div className={cn("text-sm", isVisible ? 'block' : 'hidden')}>{children}</div>
    </div>
}
