<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Addresses\DeleteAddressAction;
use App\Actions\Addresses\StoreAddressForAccountAction;
use App\Actions\Addresses\StoreAddressForContactAction;
use App\Actions\Addresses\UpdateAddressForAccountAction;
use App\Actions\Addresses\UpdateAddressForContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addresses\AddressFormRequest;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Throwable;

final class AddressController extends Controller
{
    public function storeForContact(
        Contact $contact,
        AddressFormRequest $request,
        StoreAddressForContactAction $action
    ): RedirectResponse {
        try {
            $action->handle($contact, $request->toDto());

            return back()->with([
                'success' => 'The address has been saved.',
            ]);
        } catch (Throwable $e) {
            return back()->withErrors([
                'name' => 'The address already exists.',
            ]);
        }
    }

    public function updateForContact(
        Contact $contact,
        Address $address,
        AddressFormRequest $request,
        UpdateAddressForContactAction $action
    ): RedirectResponse {
        try {
            $action->handle($contact, $address, $request->toDto());

            return back()->with([
                'success' => 'The address has been updated.',
            ]);
        } catch (Throwable $e) {
            return back()->withErrors([
                'name' => 'Something went wrong updating the address.',
            ]);
        }
    }

    public function storeForAccount(
        Account $account,
        AddressFormRequest $request,
        StoreAddressForAccountAction $action
    ): RedirectResponse {
        $action->handle($account, $request->toDto());

        return back()->with([
            'success' => 'The address has been added.',
        ]);
    }

    public function updateForAccount(
        Account $account,
        Address $address,
        AddressFormRequest $request,
        UpdateAddressForAccountAction $action
    ): RedirectResponse {
        try {
            $action->handle($account, $address, $request->toDto());

            return back()->with([
                'success' => 'The address has been updated.',
            ]);
        } catch (Throwable $e) {
            return back()->withErrors([
                'name' => 'Something went wrong updating the address.',
            ]);
        }
    }

    public function destroy(
        Address $address,
        DeleteAddressAction $action
    ): RedirectResponse {
        $action->handle($address);

        return back()->with([
            'success' => 'The address has been deleted.',
        ]);
    }
}
