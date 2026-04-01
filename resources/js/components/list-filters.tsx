import {Form, Link} from "@inertiajs/react";
import {ChevronDownIcon, ChevronUpIcon, XIcon} from "lucide-react";
import type {PropsWithChildren} from "react";
import {useState, useRef} from "react";
import {Button} from "@/components/ui/button";
import {cn} from "@/lib/utils";

type Props = PropsWithChildren<{
    action: string;
}>;

export default function ListFilters({children, action}: Props) {
    const dropdownRef = useRef<HTMLDivElement>(null);
    const [isVisible, setIsVisible] = useState(false);
    const toggleFilters = () => {
        setIsVisible(!isVisible);
    };

    return <div className="dashboard-list-filters relative z-40">
        <Button
            onClick={toggleFilters}
        >
            Filters
            <ChevronDownIcon className={isVisible ? 'hidden' : 'inline-flex'}/>
            <ChevronUpIcon className={isVisible ? 'inline-flex' : 'hidden'}/>
        </Button>
        <Form action={action} method="GET">
            <div ref={dropdownRef} className={cn("w-full md:w-88 fixed inset-0 md:absolute md:top-[110%] md:right-0 md:left-auto md:bottom-auto overflow-hidden md:rounded-lg md:border md:border-gray-300 md:shadow-lg", isVisible ? 'flex' : 'hidden')}>
                <div className="text-gray-800 w-full flex flex-col gap-4 bg-white  p-4  md:max-h-96 overflow-y-auto">
                    <div className="flex items-center justify-between gap-3">
                        <span className="font-semibold">Filters</span>
                        <Button
                            className="!px-0"
                            onClick={toggleFilters}
                            variant="link"
                            type="button"
                        >
                            <XIcon size={16} className="text-black"/>
                        </Button>
                    </div>
                    {children}
                    <div className="flex flex-wrap items-center justify-end gap-3">
                        <Button
                            variant="secondary"
                            type="reset"
                            asChild
                        >
                            <Link
                                href={action}
                            >
                                Reset
                            </Link>
                        </Button>

                        <Button
                            type="submit"
                        >
                            Apply
                        </Button>
                    </div>
                </div>
            </div>
        </Form>
    </div>
}
