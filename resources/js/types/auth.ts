export type User = {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    formatted_role: string | null;
    created_at: string;
    formatted_created_at: string;
    email_verified_at: string | null;
    formatted_email_verified_at: string | null;
    permission_names?: string[];
    role_names?: string[];
};

export type Auth = {
    user: User;
};
