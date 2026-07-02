import {Link} from "@inertiajs/react";
import {ChevronLeftIcon, ChevronRightIcon} from "lucide-react";
import type {ReactNode} from "react";
import {Button} from "@/components/ui/button";
import type {PaginatedCollection} from "@/types";

type Props = {
    collection: PaginatedCollection<unknown>
};

export function CollectionPagination({collection}: Props) {
    return <div className="flex items-center justify-between">
        <div className="text-muted-foreground hidden flex-1 text-sm lg:flex">
            Page {collection.meta.current_page} of {collection.meta.last_page}
        </div>

        {collection.meta.last_page > 1 && (
            <nav role="navigation" aria-label="Pagination">
                <ul className="flex items-center gap-1">
                    {collection.meta.links.map((link, index) => (
                        <li key={index}>
                            <Button
                                asChild
                                disabled={link.url === null}
                                aria-current={link.active ? 'page' : undefined}
                                data-active={link.active}
                                variant={link.active ? 'outline' : 'ghost'}
                                className={link.url === null ? 'pointer-events-none' : ''}
                            >
                                <Link href={link.url ?? '#'}>{label(link.label, index, collection.meta.links.length)}</Link>
                            </Button>
                        </li>
                    ))}
                </ul>
            </nav>
        )}
    </div>
}

function label(s: string, index: number, count: number): ReactNode {
    if (index === 0) {
        return <ChevronLeftIcon/>;
    }

    if (index === count - 1) {
        return <ChevronRightIcon/>;
    }

    return s;
}
