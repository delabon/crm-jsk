import {Dialog} from "@base-ui/react/dialog";
import {XIcon} from "lucide-react";
import type * as React from "react";

import {cn} from "@/lib/utils";

type ModalProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title: string;
    description?: string;
    children: React.ReactNode;
    className?: string;
};

export function Modal({open, onOpenChange, title, description, children, className}: ModalProps) {
    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange} modal>
            <Dialog.Portal>
                <Dialog.Backdrop
                    className="fixed inset-0 z-50 bg-black/80 data-open:animate-in data-open:fade-in-0 data-closed:animate-out data-closed:fade-out-0"
                />
                <Dialog.Popup
                    className={cn(
                        "bg-background data-open:animate-in data-open:fade-in-0 data-open:zoom-in-95 data-closed:animate-out data-closed:fade-out-0 data-closed:zoom-out-95 fixed top-[50%] left-[50%] z-50 grid w-full max-w-[calc(100%-2rem)] -translate-x-1/2 -translate-y-1/2 gap-4 rounded-lg border p-6 shadow-lg duration-200 sm:max-w-lg",
                        className,
                    )}
                >
                    <div className="flex flex-col gap-2 text-center sm:text-left">
                        <Dialog.Title className="text-lg leading-none font-semibold">
                            {title}
                        </Dialog.Title>
                        {description && (
                            <Dialog.Description className="text-muted-foreground text-sm">
                                {description}
                            </Dialog.Description>
                        )}
                    </div>

                    {children}

                    <Dialog.Close
                        aria-label="Close"
                        className="ring-offset-background focus:ring-ring absolute top-4 right-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 focus:ring-2 focus:ring-offset-2 focus:outline-hidden disabled:pointer-events-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4"
                    >
                        <XIcon />
                        <span className="sr-only">Close</span>
                    </Dialog.Close>
                </Dialog.Popup>
            </Dialog.Portal>
        </Dialog.Root>
    );
}
