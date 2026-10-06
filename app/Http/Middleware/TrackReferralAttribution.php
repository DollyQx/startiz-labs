<?php

namespace App\Http\Middleware;

use App\Services\ReferralService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferralAttribution
{
    public function __construct(
        protected ReferralService $referralService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $code = $this->referralService->captureFromRequest($request);

        $response = $next($request);

        if ($code && method_exists($response, 'cookie')) {
            $lifetimeMinutes = (int) config('partner.cookie_lifetime_days', 30) * 24 * 60;
            $response->cookie(ReferralService::COOKIE_KEY, $code, $lifetimeMinutes);
        }

        return $response;
    }
}
