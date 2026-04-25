<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Contacts\GetPaginatedContactAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class ContactController extends Controller
{
    public function index(Request $request, GetPaginatedContactAction $action): InertiaResponse
    {
        /** @var User $user */
        $user = $request->user();

        $contacts = $action->handle(Config::integer('app.dashboard.per_page'), $user);

        return Inertia::render('contacts/index', [
            'collection' => ContactResource::collection($contacts),
        ]);
    }
}
