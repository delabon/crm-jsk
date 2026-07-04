import {MapPinIcon, PencilIcon, PlusIcon} from "lucide-react";
import {useState} from "react";
import AddressForm from "@/components/address-form";
import DeleteButton from "@/components/delete-button";
import {Button} from "@/components/ui/button";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Modal} from "@/components/ui/modal";
import addressesRoutes from "@/routes/addresses";
import storeForAccount from "@/routes/addresses/store/for";
import updateForAccount from "@/routes/addresses/update/for";
import type {Account, Address, SelectOption} from "@/types";

type Props = {
    account: Account;
    countries: SelectOption[];
    can: {
        create_address: boolean;
        update_address: boolean;
        delete_address: boolean;
    };
};

export default function AccountAddresses({account, countries, can}: Props) {
    const [showAdd, setShowAdd] = useState(false);
    const [editing, setEditing] = useState<Address | null>(null);

    const addresses = account.addresses ?? [];
    const storeForm = storeForAccount.account.form(account.id);
    const updateForm = editing
        ? updateForAccount.account.form({account: account.id, address: editing.id})
        : null;

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between gap-2">
                <CardTitle className="flex items-center gap-2">
                    <MapPinIcon className="h-4 w-4" />
                    Addresses
                </CardTitle>

                {can.create_address && (
                    <Button variant="outline" size="sm" onClick={() => setShowAdd(true)}>
                        <PlusIcon className="mr-1 h-4 w-4" />
                        Add address
                    </Button>
                )}
            </CardHeader>

            <CardContent>
                {addresses.length > 0 ? (
                    <ul className="divide-y">
                        {addresses.map((address) => (
                            <li
                                key={address.id}
                                className="flex flex-wrap items-start justify-between gap-3 py-3 first:pt-0 last:pb-0"
                            >
                                <address className="not-italic space-y-0.5 text-sm leading-relaxed text-muted-foreground">
                                    {address.name && (
                                        <span className="block font-medium text-foreground">
                                            {address.name}
                                        </span>
                                    )}
                                    <span className="block">{address.line1}</span>
                                    {address.line2 && (
                                        <span className="block">{address.line2}</span>
                                    )}
                                    <span className="block">
                                        {[address.city, address.region_name]
                                            .filter(Boolean)
                                            .join(", ")}
                                        {address.postal_code
                                            ? ` ${address.postal_code}`
                                            : ""}
                                    </span>
                                    {address.country_name && (
                                        <span className="block">{address.country_name}</span>
                                    )}
                                </address>

                                {(can.update_address || can.delete_address) && (
                                    <div className="flex shrink-0 items-center gap-2">
                                        {can.update_address && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => setEditing(address)}
                                            >
                                                <PencilIcon className="mr-1 h-4 w-4" />
                                                Edit
                                            </Button>
                                        )}
                                        {can.delete_address && (
                                            <DeleteButton
                                                size="sm"
                                                message="Are you sure you want to delete this address?"
                                                {...addressesRoutes.destroy.form(address.id)}
                                            />
                                        )}
                                    </div>
                                )}
                            </li>
                        ))}
                    </ul>
                ) : (
                    <div className="flex flex-col items-center justify-center py-8 text-center">
                        <div className="mb-3 rounded-full bg-muted p-3">
                            <MapPinIcon className="h-6 w-6 text-muted-foreground" />
                        </div>
                        <p className="text-sm font-medium">No addresses yet</p>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Add an address to get started.
                        </p>
                    </div>
                )}
            </CardContent>

            <Modal
                open={showAdd}
                onOpenChange={setShowAdd}
                title="Add address"
                description="Add a new address for this account."
            >
                <AddressForm
                    key="new"
                    action={storeForm.action}
                    method={storeForm.method}
                    countries={countries}
                    onSuccess={() => setShowAdd(false)}
                />
            </Modal>

            <Modal
                open={!!editing}
                onOpenChange={(open) => !open && setEditing(null)}
                title="Edit address"
                description="Update the details for this address."
            >
                {editing && updateForm && (
                    <AddressForm
                        key={editing.id}
                        action={updateForm.action}
                        method={updateForm.method}
                        countries={countries}
                        name={editing.name}
                        line1={editing.line1}
                        line2={editing.line2}
                        city={editing.city}
                        postalCode={editing.postal_code}
                        countryId={editing.country_id}
                        regionId={editing.region_id ?? ""}
                        initialRegions={
                            editing.region_id && editing.region_name
                                ? [
                                    {
                                        value: editing.region_id,
                                        label: editing.region_name,
                                    },
                                ]
                                : []
                        }
                        onSuccess={() => setEditing(null)}
                    />
                )}
            </Modal>
        </Card>
    );
}
