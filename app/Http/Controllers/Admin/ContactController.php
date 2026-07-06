<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Contacts\DeleteContactAction;
use App\Actions\Contacts\GetPaginatedContactAction;
use App\Actions\Contacts\StoreContactAction;
use App\Actions\Contacts\UpdateContactAction;
use App\Actions\Countries\GetCountryOptionsAction;
use App\Enums\ContactStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contacts\ContactFormRequest;
use App\Http\Requests\Admin\Contacts\IndexContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ContactController extends Controller
{
    public function index(IndexContactRequest $request, GetPaginatedContactAction $action): InertiaResponse
    {
        /** @var User $user */
        $user = $request->user();
        $contactFilterDto = $request->toDto();

        $contacts = $action->handle(
            Config::integer('app.dashboard.per_page'),
            $user,
            $contactFilterDto
        )->appends(array_filter($contactFilterDto->toArray()));

        return Inertia::render('contacts/index', [
            'collection' => ContactResource::collection($contacts),
            'statuses' => [
                [
                    'value' => 'all',
                    'label' => 'All',
                ],
                ...ContactStatus::options(),
            ],
            'filters' => [
                'status' => $request->status ?? 'all',
            ],
            'search' => $request->search,
        ]);
    }

    public function create(GetCountryOptionsAction $getCountryOptionsAction): InertiaResponse
    {
        return Inertia::render('contacts/create', [
            'statuses' => ContactStatus::options(),
            'countries' => $getCountryOptionsAction->handle(),
        ]);
    }

    public function store(ContactFormRequest $request, StoreContactAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->toDto());

        return to_route('contacts.index')
            ->with('success', 'The contact has been created.');
    }

    public function show(Request $request, Contact $contact): InertiaResponse
    {
        $contact->load(['user', 'account', 'address.country', 'address.region']);

        /** @var User $user */
        $user = $request->user();

        return Inertia::render('contacts/show', [
            'contact' => new ContactResource($contact),
            'can' => [
                'update' => $user->can('update', $contact),
                'delete' => $user->can('delete', $contact),
            ],
        ]);
    }

    public function edit(Contact $contact, Request $request, GetCountryOptionsAction $getCountryOptionsAction): InertiaResponse
    {
        $contact->load(['user', 'account', 'address.country', 'address.region']);
        $user = $request->user();

        return Inertia::render('contacts/edit', [
            'contact' => new ContactResource($contact),
            'statuses' => ContactStatus::options(),
            'countries' => $getCountryOptionsAction->handle(),
            'can' => [
                'create_address' => $user->can('create', [Address::class, $contact]),
                'update_address' => $user->can('addresses.update'),
            ],
        ]);
    }

    public function update(ContactFormRequest $request, Contact $contact, UpdateContactAction $action): RedirectResponse
    {
        $action->handle($contact, $request->toDto());

        return to_route('contacts.index')
            ->with('success', 'The contact #'.$contact->id.' has been updated.');
    }

    public function destroy(Contact $contact, DeleteContactAction $action): RedirectResponse
    {
        $id = $action->handle($contact);

        return to_route('contacts.index')
            ->with('success', 'The contact #'.$id.' has been deleted.');
    }
}
