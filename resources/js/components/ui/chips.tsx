import { useState, useCallback, useMemo, useEffect, useRef } from "react"
import { LoaderIcon, XIcon } from "lucide-react"

import { cn } from "@/lib/utils"
import type { SelectOption } from "@/types"
import {
    Combobox,
    ComboboxChips,
    ComboboxChip,
    ComboboxChipsInput,
    ComboboxContent,
    ComboboxList,
    ComboboxItem,
    ComboboxEmpty,
    useComboboxAnchor,
} from "@/components/ui/combobox"

type ChipsProps = {
    name: string
    endpoint: string
    multiple?: boolean
    defaultValue?: SelectOption | SelectOption[]
    placeholder?: string
    searchParam?: string
    id?: string
    disabled?: boolean
    className?: string
}

export function Chips({
    name,
    endpoint,
    multiple = false,
    defaultValue,
    placeholder = "Search...",
    searchParam = "search",
    id,
    disabled = false,
    className,
}: ChipsProps) {
    const anchor = useComboboxAnchor()

    const [selectedItems, setSelectedItems] = useState<SelectOption[]>(() => {
        if (!defaultValue) {
            return [];
        }

        return Array.isArray(defaultValue) ? defaultValue : [defaultValue];
    })

    const [inputValue, setInputValue] = useState("");
    const [results, setResults] = useState<SelectOption[]>([]);
    const [loading, setLoading] = useState(false);
    const [open, setOpen] = useState(false);

    const abortRef = useRef<AbortController | null>(null);
    const debounceRef = useRef<number | null>(null);

    const search = useCallback(
        (query: string) => {
            if (debounceRef.current !== null) {
                clearTimeout(debounceRef.current);
            }

            if (abortRef.current) {
                abortRef.current.abort();
            }

            if (!query.trim()) {
                setResults([]);
                setLoading(false);

                return;
            }

            debounceRef.current = window.setTimeout(async () => {
                const controller = new AbortController();
                abortRef.current = controller;
                setLoading(true);

                try {
                    const url = new URL(endpoint, window.location.origin);
                    url.searchParams.set(searchParam, query);
                    const res = await fetch(url.toString(), {
                        signal: controller.signal,
                        headers: { Accept: "application/json" },
                        method: 'post',
                    });
                    const data: SelectOption[] = await res.json();
                    console.log(data)
                    setResults(data);
                } catch (err) {
                    if (err instanceof DOMException && err.name === "AbortError") {
                        return;
                    }

                    setResults([]);
                } finally {
                    setLoading(false);
                }
            }, 300)
        },
        [endpoint, searchParam],
    )

    const filteredResults = useMemo(() => {
        if (!multiple) return results
        const selected = new Set(selectedItems.map((i) => i.value))
        return results.filter((r) => !selected.has(r.value))
    }, [results, selectedItems, multiple]);

    const handleValueChange = useCallback(
        (value: SelectOption[]) => {
            if (!multiple) {
                if (value.length === 0) {
                    setSelectedItems([])
                } else {
                    setSelectedItems([value[value.length - 1]])
                }
            } else {
                setSelectedItems(value)
            }
        },
        [multiple],
    );

    const handleInputValueChange = useCallback(
        (newInputValue: string, details: { reason: string }) => {
            if (details.reason !== 'input-change' && details.reason !== 'input-paste') return
            setInputValue(newInputValue)
            search(newInputValue)
            if (newInputValue.length > 0) {
                setOpen(true)
            }
        },
        [search],
    );

    const handleClear = useCallback(() => {
        setInputValue("")
        setResults([])
        setOpen(false)
    }, [])

    useEffect(() => {
        return () => {
            if (debounceRef.current !== null) clearTimeout(debounceRef.current)
            if (abortRef.current) abortRef.current.abort()
        }
    }, []);

    return (
        <div className={cn("relative", className)}>
            {!multiple && selectedItems[0] && (
                <input type="hidden" name={name} value={selectedItems[0].value} />
            )}
            {multiple &&
                selectedItems.map((item) => (
                    <input
                        key={item.value}
                        type="hidden"
                        name={`${name}[]`}
                        value={item.value}
                    />
                ))}

            <Combobox<SelectOption, true>
                value={selectedItems}
                onValueChange={handleValueChange}
                inputValue={inputValue}
                onInputValueChange={handleInputValueChange}
                open={open}
                onOpenChange={setOpen}
                multiple
                disabled={disabled}
                filter={() => true}
                isItemEqualToValue={(a: SelectOption, b: SelectOption) => a.value === b.value}
            >
                <div ref={anchor} className="relative">
                    <ComboboxChips>
                        {selectedItems.map((item, index) => (
                            <ComboboxChip key={index}>
                                {item.label}
                            </ComboboxChip>
                        ))}
                        <ComboboxChipsInput
                            id={id}
                            placeholder={selectedItems.length > 0 ? "" : placeholder}
                            className={inputValue ? "pr-7" : undefined}
                        />
                    </ComboboxChips>
                    {inputValue && (
                        <button
                            type="button"
                            onClick={handleClear}
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <XIcon className="size-4 pointer-events-none" />
                        </button>
                    )}
                </div>

                <ComboboxContent anchor={anchor}>
                    <ComboboxList>
                        {loading ? (
                            <div className="flex items-center justify-center py-3">
                                <LoaderIcon className="size-4 animate-spin text-muted-foreground" />
                            </div>
                        ) : (
                            filteredResults.map((item) => (
                                <ComboboxItem key={item.value} value={item}>
                                    {item.label}
                                </ComboboxItem>
                            ))
                        )}
                        {!loading && filteredResults.length === 0 && (
                            <ComboboxEmpty>No results found</ComboboxEmpty>
                        )}
                    </ComboboxList>
                </ComboboxContent>
            </Combobox>
        </div>
    )
}
