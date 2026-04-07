export type * from './auth';
export type * from './navigation';
export type * from './ui';

export type PaginatedCollection<T> = {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    },
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    }
};

export type RoleOption = {
    value: string;
    label: string;
}

export type Account = {
    id: number;
    name: string;
    industry: string;
    website: string;
    phone: string;
    owner: string;
    formatted_created_at: string;
};
