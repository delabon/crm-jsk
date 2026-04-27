import { Head, Link, usePage } from '@inertiajs/react';
import {
    Building2,
    Plus,
    TrendingUp,
    UserPlus,
    UserCog,
    ArrowRight,
    CalendarDays,
    GlobeIcon,
    UsersIcon,
    BookUserIcon,
} from 'lucide-react';
import React from "react";
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useInitials } from '@/hooks/use-initials';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import accountRoutes from '@/routes/accounts';
import userRoutes from '@/routes/users';
import contactRoutes from '@/routes/contacts';
import type { Account, BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

type StatCardConfig = {
    key: string;
    label: string;
    value: number;
    description: string;
    icon: React.ElementType;
    color: string;
    bgColor: string;
};

type StatCards = {
    accounts: StatCardConfig[];
    contacts: StatCardConfig[];
    users: StatCardConfig[];
};

type RoleDistribution = {
    role: string;
    count: number;
};

type Props = {
    metrics: {
        stats: {
            my_accounts: number;
            total_accounts?: number;
            accounts_this_month: number;

            my_contacts: number;
            total_contacts?: number;
            contacts_this_month: number;

            total_users?: number;
        };
        recent_accounts: {
            data: Account[];
        };
        role_distribution?: RoleDistribution[] | null;
    };
};

function getGreeting(): string {
    const hour = new Date().getHours();

    if (hour < 12) {
        return 'Good morning';
    }

    if (hour < 17) {
        return 'Good afternoon';
    }

    return 'Good evening';
}

function formatDate(): string {
    return new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function getStatCards(stats: Props['metrics']['stats']): StatCards {
    const statCards = {
        accounts: [
            {
                key: 'my_accounts',
                label: 'My Accounts',
                value: stats.my_accounts,
                description: 'Accounts assigned to you',
                icon: Building2,
                color: 'text-blue-600 dark:text-blue-400',
                bgColor: 'bg-blue-50 dark:bg-blue-950/50',
            },
            {
                key: 'accounts_this_month',
                label: 'New This Month',
                value: stats.accounts_this_month,
                description: 'Accounts assigned to you this month',
                icon: TrendingUp,
                color: 'text-emerald-600 dark:text-emerald-400',
                bgColor: 'bg-emerald-50 dark:bg-emerald-950/50',
            }
        ],
        contacts: [
            {
                key: 'my_contacts',
                label: 'My Contacts',
                value: stats.my_contacts,
                description: 'Contacts assigned to you',
                icon: BookUserIcon,
                color: 'text-blue-600 dark:text-blue-400',
                bgColor: 'bg-blue-50 dark:bg-blue-950/50',
            },
            {
                key: 'contacts_this_month',
                label: 'New This Month',
                value: stats.contacts_this_month,
                description: 'Contacts assigned to you this month',
                icon: TrendingUp,
                color: 'text-emerald-600 dark:text-emerald-400',
                bgColor: 'bg-emerald-50 dark:bg-emerald-950/50',
            }
        ],
        users: [],
    };

    if (stats.total_accounts !== undefined) {
        statCards.accounts.push({
            key: 'total_accounts',
            label: 'Total Accounts',
            value: stats.total_accounts,
            description: 'Across all team members',
            icon: GlobeIcon,
            color: 'text-purple-600 dark:text-purple-400',
            bgColor: 'bg-purple-50 dark:bg-purple-950/50',
        });
    }

    if (stats.total_contacts !== undefined) {
        statCards.contacts.push({
            key: 'total_contacts',
            label: 'Total Contacts',
            value: stats.total_contacts,
            description: 'Across all team members',
            icon: GlobeIcon,
            color: 'text-purple-600 dark:text-purple-400',
            bgColor: 'bg-purple-50 dark:bg-purple-950/50',
        });
    }

    if (stats.total_users !== undefined) {
        // @ts-ignore
        statCards.users.push({
            key: 'total_users',
            label: 'Total Users',
            value: stats.total_users,
            description: 'All system users',
            icon: UsersIcon,
            color: 'text-amber-600 dark:text-amber-400',
            bgColor: 'bg-amber-50 dark:bg-amber-950/50',
        });
    }

    return statCards;
}

function getRoleBadgeVariant(
    role: string | null,
): 'default' | 'secondary' | 'outline' | 'destructive' {
    switch (role) {
        case 'super_admin':
            return 'default';
        case 'manager':
            return 'secondary';
        default:
            return 'outline';
    }
}

function formatRoleLabel(role: string | null): string {
    if (!role) {
        return '';
    }

    return role.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

const ROLE_COLORS: Record<string, string> = {
    'Super Admin': 'bg-blue-500',
    Manager: 'bg-purple-500',
    'Sales Agent': 'bg-emerald-500',
    User: 'bg-gray-400',
};

export default function Dashboard({ metrics }: Props) {
    const { user } = usePage().props.auth;
    const getInitials = useInitials();
    const { stats, recent_accounts, role_distribution } = metrics;
    const statCards = getStatCards(stats);
    const permissions = user.permission_names ?? [];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            <div className="space-y-6 p-6">
                <div className="flex flex-col gap-1">
                    <div className="flex items-center gap-3">
                        <h1 className="text-2xl font-bold tracking-tight">
                            {getGreeting()}, {user.first_name}
                        </h1>
                        <Badge variant={getRoleBadgeVariant(user.main_role)}>
                            {formatRoleLabel(user.formatted_role)}
                        </Badge>
                    </div>
                    <p className="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <CalendarDays className="h-3.5 w-3.5" />
                        {formatDate()}
                    </p>
                </div>

                {Object.values(statCards).map((cards, index) => (
                    <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" key={`card-${index}`}>
                        {cards.map((card) => (
                            <Card key={card.key} className="relative overflow-hidden">
                                <CardContent className="p-5">
                                    <div className="flex items-start justify-between">
                                        <div className="space-y-1">
                                            <p className="text-sm font-medium text-muted-foreground">
                                                {card.label}
                                            </p>
                                            <p className="text-3xl font-bold tracking-tight">
                                                {card.value?.toLocaleString()}
                                            </p>
                                        </div>
                                        <div className={`rounded-lg p-2.5 ${card.bgColor}`}>
                                            <card.icon
                                                className={`h-5 w-5 ${card.color}`}
                                            />
                                        </div>
                                    </div>
                                    <p className="mt-2 text-xs text-muted-foreground">
                                        {card.description}
                                    </p>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                ))}

                {permissions.length > 0 && (
                    <div className="flex flex-wrap items-center gap-2">
                        <span className="text-sm font-medium text-muted-foreground">
                            Quick actions:
                        </span>
                        {permissions.includes('accounts.create') && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={accountRoutes.create()}>
                                    <Plus className="mr-1.5 h-3.5 w-3.5" />
                                    New Account
                                </Link>
                            </Button>
                        )}
                        {permissions.includes('contacts.create') && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={contactRoutes.create()}>
                                    <Plus className="mr-1.5 h-3.5 w-3.5" />
                                    New Contact
                                </Link>
                            </Button>
                        )}
                        {permissions.includes('users.manage') && (
                            <Button variant="outline" size="sm" asChild>
                                <Link href={userRoutes.create()}>
                                    <UserPlus className="mr-1.5 h-3.5 w-3.5" />
                                    New User
                                </Link>
                            </Button>
                        )}
                    </div>
                )}

                <Separator />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between pb-2">
                                <CardTitle className="flex items-center gap-2 text-base">
                                    <Building2 className="h-4 w-4" />
                                    Recent Accounts
                                </CardTitle>
                                {permissions.includes('accounts.view-own') && (
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        asChild
                                    >
                                        <Link href={accountRoutes.index()}>
                                            View all
                                            <ArrowRight className="ml-1 h-3.5 w-3.5" />
                                        </Link>
                                    </Button>
                                )}
                            </CardHeader>
                            <CardContent className="pt-0">
                                {recent_accounts.data.length === 0 ? (
                                    <div className="flex flex-col items-center justify-center py-10 text-center">
                                        <div className="mb-3 rounded-full bg-muted p-3">
                                            <Building2 className="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <p className="text-sm font-medium">
                                            No accounts yet
                                        </p>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            {permissions.includes(
                                                'accounts.create',
                                            )
                                                ? 'Create your first account to get started.'
                                                : 'Accounts will appear here once created.'}
                                        </p>
                                    </div>
                                ) : (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Name</TableHead>
                                                <TableHead>Industry</TableHead>
                                                <TableHead>Owner</TableHead>
                                                <TableHead>Created</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {recent_accounts.data.map(
                                                (account) => (
                                                    <TableRow
                                                        key={account.id}
                                                    >
                                                        <TableCell>
                                                            <Link
                                                                href={accountRoutes.show(
                                                                    account.id,
                                                                )}
                                                                className="font-medium text-primary hover:underline"
                                                            >
                                                                {account.name}
                                                            </Link>
                                                        </TableCell>
                                                        <TableCell>
                                                            {account.industry ? (
                                                                <Badge variant="secondary">
                                                                    {
                                                                        account.industry
                                                                    }
                                                                </Badge>
                                                            ) : (
                                                                '—'
                                                            )}
                                                        </TableCell>
                                                        <TableCell>
                                                            {account.owner ? (
                                                                <div className="flex items-center gap-2">
                                                                    <Avatar className="h-6 w-6">
                                                                        <AvatarFallback className="bg-neutral-200 text-[10px] text-black dark:bg-neutral-700 dark:text-white">
                                                                            {getInitials(account.owner.name ?? '')}
                                                                        </AvatarFallback>
                                                                    </Avatar>
                                                                    <span className="text-sm">
                                                                        {account.owner.name}
                                                                    </span>
                                                                </div>
                                                            ) : (
                                                                '—'
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">
                                                            {
                                                                account.formatted_created_at
                                                            }
                                                        </TableCell>
                                                    </TableRow>
                                                ),
                                            )}
                                        </TableBody>
                                    </Table>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {role_distribution && role_distribution.length > 0 && (
                        <div>
                            <Card className="h-full">
                                <CardHeader className="pb-3">
                                    <CardTitle className="flex items-center gap-2 text-base">
                                        <UserCog className="h-4 w-4" />
                                        Team Overview
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4 pt-0">
                                    {role_distribution.map((item) => {
                                        const maxCount = Math.max(
                                            ...role_distribution.map(
                                                (r) => r.count,
                                            ),
                                        );
                                        const widthPercent =
                                            maxCount > 0
                                                ? (item.count / maxCount) * 100
                                                : 0;

                                        return (
                                            <div
                                                key={item.role}
                                                className="space-y-1.5"
                                            >
                                                <div className="flex items-center justify-between text-sm">
                                                    <span className="font-medium">
                                                        {item.role}
                                                    </span>
                                                    <span className="text-muted-foreground">
                                                        {item.count}
                                                    </span>
                                                </div>
                                                <div className="h-2 w-full overflow-hidden rounded-full bg-muted">
                                                    <div
                                                        className={`h-full rounded-full transition-all ${ROLE_COLORS[item.role] ?? 'bg-gray-400'}`}
                                                        style={{
                                                            width: `${widthPercent}%`,
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </CardContent>
                            </Card>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
