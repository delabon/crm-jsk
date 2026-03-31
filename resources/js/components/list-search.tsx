import {Form} from '@inertiajs/react';
import { router } from '@inertiajs/react'
import {SearchIcon, XIcon} from "lucide-react";
import React, {useState} from "react";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";

type Props = {
    action: string;
    initialSearch?: string;
};

export default function ListSearch({action, initialSearch}: Props) {
    const [search, setSearch] = useState(initialSearch ?? '');

    const handleQueryChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setSearch(e.target.value);
    };

    const handleReset = () => {
        setSearch('');

        const params = new URLSearchParams(window.location.search);

        if (params.get('search') && params.get('search') !== '') {
            handleUrl('');
        }
    };

    const handleSubmit = (e: React.SubmitEvent<HTMLFormElement>) => {
        e.preventDefault();

        handleUrl(search);

        return false;
    };

    const handleUrl = (newSearch: string) => {
        const params = new URLSearchParams(window.location.search);

        if (newSearch === '') {
            params.delete('search');
        } else {
            params.set('search', newSearch);
        }

        params.delete('page'); // Reset pagination

        router.visit(window.location.pathname, {
            data: Object.fromEntries(params.entries()),
            preserveState: true,
        })
    };

    return <Form action={action} method="GET" onSubmit={handleSubmit}>
        {({errors, processing}) => (
            <div className="flex relative">
                <Input
                    name="search"
                    placeholder="Search..."
                    className="min-w-64 pr-18"
                    onChange={handleQueryChange}
                    aria-invalid={!!errors['search']}
                    value={search}
                />
                <div className="inline-flex items-center absolute right-0">
                    {(search && search !== '') &&
                        <Button
                            variant="link"
                            size="icon"
                            type="button"
                            disabled={processing}
                            onClick={handleReset}
                        >
                            <XIcon
                                size={16}
                            />
                        </Button>
                    }

                    <Button
                        variant="link"
                        size="icon"
                        type="submit"
                        disabled={processing}
                    >
                        <SearchIcon
                            size={16}
                        />
                    </Button>
                </div>
            </div>
        )}
    </Form>
}
