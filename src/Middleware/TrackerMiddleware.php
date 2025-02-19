<?php

namespace Mixedtype\Tracker\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mixedtype\Tracker\Tracker;
use Symfony\Component\HttpFoundation\Response;

class TrackerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Tracker::getInstance(base_path())
                ->trackAppBegin()
                ->saveApp();

        return $next($request);
    }

    public function terminate($request, $response): void
    {
        \Mixedtype\Tracker\Tracker::getInstance()->trackAppTerminateAndSave();
    }
}
