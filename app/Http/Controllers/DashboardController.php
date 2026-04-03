<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardMetricsAction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class DashboardController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, GetDashboardMetricsAction $action): InertiaResponse
    {
        return Inertia::render('dashboard', [
            'metrics' => $action->handle($request->user()),
        ]);
    }
}
