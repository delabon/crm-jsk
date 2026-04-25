<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Contacts\GetPaginatedContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contacts\IndexContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ContactController extends Controller
{
    public function index(IndexContactRequest $request, GetPaginatedContactAction $action): InertiaResponse
    {
        /** @var User $user */
        $user = $request->user();

        $contacts = $action->handle(
            Config::integer('app.dashboard.per_page'),
            $user,
            $request->toDto()
        );

        return Inertia::render('contacts/index', [
            'collection' => ContactResource::collection($contacts),
        ]);
    }
}
