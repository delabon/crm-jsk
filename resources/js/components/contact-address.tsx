import {MapPinIcon, PencilIcon, PlusIcon} from "lucide-react";
import {useState} from "react";
import AddressForm from "@/components/address-form";
import DeleteButton from "@/components/delete-button";
import {Button} from "@/components/ui/button";
import {Card, CardContent, CardHeader, CardTitle} from "@/components/ui/card";
import {Modal} from "@/components/ui/modal";
import addressesRoutes from "@/routes/addresses";
import storeForContact from "@/routes/addresses/store";
import updateForContact from "@/routes/addresses/update/for";
import type {Contact, SelectOption} from "@/types";

type Props = {
    contact: Contact;
    countries: SelectOption[];
    can: {
        create_address: boolean;
        update_address: boolean;
        delete_address: boolean;
    };
};

export default function ContactAddress({contact, countries, can}: Props) {
    const [open, setOpen] = useState(false);
    const address = contact.address;
    const hasAddress = !!address;

    const storeForm = storeForContact.for.contact.form(contact.id);
    const updateForm = address
        ? updateForContact.contact.form({contact: contact.id, address: address.id})
        : null;

    const canManage = hasAddress ? can.update_address : can.create_address;

    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between gap-2">
                <CardTitle className="flex items-center gap-2">
                    <MapPinIcon className="h-4 w-4" />
                    Address
                </CardTitle>

                <div className="flex flex-row items-center gap-2">
                    {canManage && (
                        <Button variant="outline" size="sm" onClick={() => setOpen(true)}>
                            {hasAddress ? (
                                <>
                                    <PencilIcon className="mr-1 h-4 w-4" />
                                    Edit
                                </>
                            ) : (
                                <>
                                    <PlusIcon className="mr-1 h-4 w-4" />
                                    Add address
                                </>
                            )}
                        </Button>
                    )}

                    {hasAddress && address && can.delete_address && (
                        <DeleteButton
                            size="sm"
                            message="Are you sure you want to delete this address?"
                            {...addressesRoutes.destroy.form(address.id)}
                        />
                    )}
                </div>
            </CardHeader>

            <CardContent>
                {hasAddress && address ? (
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
                            {[address.city, address.region_name].filter(Boolean).join(", ")}
                            {address.postal_code ? ` ${address.postal_code}` : ""}
                        </span>
                        {address.country_name && (
                            <span className="block">{address.country_name}</span>
                        )}
                    </address>
                ) : (
                    <div className="flex flex-col items-center justify-center py-8 text-center">
                        <div className="mb-3 rounded-full bg-muted p-3">
                            <MapPinIcon className="h-6 w-6 text-muted-foreground" />
                        </div>
                        <p className="text-sm font-medium">No address yet</p>
                        <p className="mt-1 text-sm text-muted-foreground">
                            Add an address to get started.
                        </p>
                    </div>
                )}
            </CardContent>

            {canManage && (
                <Modal
                    open={open}
                    onOpenChange={setOpen}
                    title={hasAddress ? "Edit address" : "Add address"}
                    description={
                        hasAddress
                            ? "Update the details for this address."
                            : "Add an address for this contact."
                    }
                >
                    {hasAddress && updateForm ? (
                        <AddressForm
                            key={address?.id}
                            action={updateForm.action}
                            method={updateForm.method}
                            countries={countries}
                            name={address?.name}
                            line1={address?.line1}
                            line2={address?.line2}
                            city={address?.city}
                            postalCode={address?.postal_code}
                            countryId={address?.country_id}
                            regionId={address?.region_id ?? ""}
                            initialRegions={
                                address?.region_id && address?.region_name
                                    ? [
                                        {
                                            value: address.region_id,
                                            label: address.region_name,
                                        },
                                    ]
                                    : []
                            }
                            onSuccess={() => setOpen(false)}
                        />
                    ) : (
                        <AddressForm
                            key="new"
                            action={storeForm.action}
                            method={storeForm.method}
                            countries={countries}
                            onSuccess={() => setOpen(false)}
                        />
                    )}
                </Modal>
            )}
        </Card>
    );
}
