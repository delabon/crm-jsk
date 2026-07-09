<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Queries\Dashboard\GetDashboardMetricsQuery;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

final class DashboardController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, GetDashboardMetricsQuery $getDashboardMetricsQuery): InertiaResponse
    {
        return Inertia::render('dashboard', [
            'metrics' => $getDashboardMetricsQuery->handle($request->user()),
        ]);
    }
}
