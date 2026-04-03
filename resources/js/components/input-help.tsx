import type { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

export default function InputHelp({
    message,
    className = '',
    ...props
}: HTMLAttributes<HTMLParagraphElement> & { message?: string }) {
    return message ? (
        <p
            {...props}
            className={cn('text-sm text-indigo-600 dark:text-indigo-400', className)}
        >
            {message}
        </p>
    ) : null;
}
