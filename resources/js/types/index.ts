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
    };
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
    };
};

export type SelectOption = {
    value: string;
    label: string;
};

export type UserBrief = {
    id: number;
    first_name: string;
    last_name: string;
    name: string | null;
    formatted_role: string | null;
};

export type Account = {
    id: number;
    name: string;
    description?: string;
    industry: string;
    website: string;
    phone: string;
    owner?: UserBrief;
    contacts?: Contact[];
    addresses?: Address[];
    formatted_created_at: string;
};

export type ContactAccount = {
    id: number;
    name: string;
};

export type Address = {
    id: number;
    name: string;
    line1: string;
    line2: string | null;
    city: string;
    region_id: string | null;
    country_id: string;
    postal_code: string;
    country_name?: string | null;
    region_name?: string | null;
};

export type Contact = {
    id: number;
    account_id?: number;
    first_name: string;
    last_name: string;
    status: string;
    status_label: string;
    email?: string;
    phone: string;
    address?: Address;
    owner?: UserBrief;
    account?: ContactAccount | null;
    formatted_created_at: string;
};
