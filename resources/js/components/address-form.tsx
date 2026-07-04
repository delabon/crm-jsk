import {Form} from "@inertiajs/react";
import {useCallback, useEffect, useRef, useState} from "react";
import {Button} from "@/components/ui/button";
import {FormField} from "@/components/ui/form-field";
import {Input} from "@/components/ui/input";
import {SelectWithItems} from "@/components/ui/select-with-items";
import {Spinner} from "@/components/ui/spinner";
import {regions as regionsRoute} from "@/routes/api/private/v1/index";
import type {SelectOption} from "@/types";

type Props = {
    action: string;
    method: "get" | "post" | "put" | "delete" | "patch" | undefined;
    name?: string;
    line1?: string;
    line2?: string | null;
    city?: string;
    postalCode?: string;
    countryId?: string;
    regionId?: string;
    countries: SelectOption[];
    initialRegions?: SelectOption[];
    onSuccess?: () => void;
}

export default function AddressForm({action, method, name, line1, line2, city, postalCode, countryId, regionId, countries, initialRegions = [], onSuccess}: Props) {
    const [selectedCountry, setSelectedCountry] = useState<string>(countryId ?? "");
    const [selectedRegion, setSelectedRegion] = useState<string>(regionId ?? "");
    const [regions, setRegions] = useState<SelectOption[]>(initialRegions);
    const [loading, setLoading] = useState<boolean>(false);
    const abortRef = useRef<AbortController | null>(null);

    useEffect(() => {
        return () => abortRef.current?.abort();
    }, []);

    const onCountryChange = useCallback(async (value: string) => {
        abortRef.current?.abort();
        setSelectedCountry(value);
        setSelectedRegion("");

        if (!value) {
            setRegions([]);

            return;
        }

        const controller = new AbortController();
        abortRef.current = controller;
        setLoading(true);

        try {
            const url = regionsRoute.url({
                query: {country_id: value},
            });

            const res = await fetch(url.toString(), {
                signal: controller.signal,
                headers: {Accept: "application/json"},
                method: "get",
            });

            const data: SelectOption[] = await res.json();
            setRegions(data);
        } catch (err) {
            if (err instanceof DOMException && err.name === "AbortError") {
                return;
            }

            setRegions([]);
        } finally {
            setLoading(false);
        }
    }, []);

    const regionPlaceholder = loading
        ? "Loading..."
        : !selectedCountry
            ? "Select country first"
            : "Select state";

    return (
        <Form action={action} method={method} className="w-full flex flex-col gap-4" onSuccess={onSuccess}>
            {({errors, processing}) => (
                <>
                    <FormField label="Name" htmlFor="name" error={errors["name"] ?? null}>
                        <Input
                            id="name"
                            name="name"
                            placeholder="Name"
                            aria-invalid={!!errors["name"]}
                            defaultValue={name}
                        />
                    </FormField>

                    <FormField label="Line 1" htmlFor="line1" error={errors["line1"] ?? null}>
                        <Input
                            id="line1"
                            name="line1"
                            placeholder="Line 1"
                            aria-invalid={!!errors["line1"]}
                            defaultValue={line1}
                        />
                    </FormField>

                    <FormField label="Line 2" htmlFor="line2" error={errors["line2"] ?? null}>
                        <Input
                            id="line2"
                            name="line2"
                            placeholder="Line 2"
                            aria-invalid={!!errors["line2"]}
                            defaultValue={line2 ?? ""}
                        />
                    </FormField>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <FormField label="City" htmlFor="city" error={errors["city"] ?? null}>
                            <Input
                                id="city"
                                name="city"
                                placeholder="City"
                                aria-invalid={!!errors["city"]}
                                defaultValue={city}
                            />
                        </FormField>

                        <FormField label="Postal code" htmlFor="postal_code" error={errors["postal_code"] ?? null}>
                            <Input
                                id="postal_code"
                                name="postal_code"
                                placeholder="Postal code"
                                aria-invalid={!!errors["postal_code"]}
                                defaultValue={postalCode}
                            />
                        </FormField>
                    </div>

                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <FormField label="Country" htmlFor="country_id" error={errors["country_id"] ?? null}>
                            <SelectWithItems
                                id="country_id"
                                name="country_id"
                                items={countries}
                                placeholder="Select country"
                                aria-invalid={!!errors["country_id"]}
                                defaultValue={countryId}
                                onValueChange={onCountryChange}
                            />
                        </FormField>

                        <FormField label="State" htmlFor="region_id" error={errors["region_id"] ?? null}>
                            <SelectWithItems
                                id="region_id"
                                name="region_id"
                                items={regions}
                                placeholder={regionPlaceholder}
                                aria-invalid={!!errors["region_id"]}
                                value={selectedRegion || null}
                                onValueChange={setSelectedRegion}
                                disabled={loading || !selectedCountry}
                            />
                        </FormField>
                    </div>

                    <div>
                        <Button
                            disabled={processing}
                            className="cursor-pointer"
                        >
                            {processing && <Spinner data-icon="inline-start" />}
                            {processing ? "Saving" : "Save"}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
