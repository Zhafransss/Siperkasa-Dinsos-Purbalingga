<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks access to all administrator routes when the panel is disabled.
 * Returns HTTP 404 Not Found so that reviewers or unauthorized visitors see
 * no trace of an admin surface. Controlled via ENABLE_ADMIN_PANEL in .env.
 */
class EnsureAdminPanelEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.admin_panel_enabled', true)) {
            abort(404);
        }

        return $next($request);
    }
}
