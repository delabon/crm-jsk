<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Contacts\GetPaginatedContactAction;
use App\Actions\Contacts\StoreContactAction;
use App\Enums\ContactStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contacts\IndexContactRequest;
use App\Http\Requests\Admin\Contacts\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

    public function create(): InertiaResponse
    {
        return Inertia::render('contacts/create', [
            'statuses' => ContactStatus::options(),
        ]);
    }

    public function store(StoreContactRequest $request, StoreContactAction $action): RedirectResponse
    {
        $action->handle($request->user(), $request->toDto());

        return to_route('contacts.index')
            ->with('success', 'The contact has been created.');
    }
}
