<?php

namespace App\Http\Middleware;

use App\Enums\PartnerStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartnerIsApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->isPartner()) {
            abort(403, 'Unauthorized access.');
        }

        $profile = $user->partnerProfile;

        if (! $profile) {
            abort(403, 'Partner profile not found.');
        }

        // Allow access to the status landing pages themselves
        if ($request->routeIs('partner.pending') || $request->routeIs('partner.suspended')) {
            return $next($request);
        }

        if ($profile->status === PartnerStatus::PENDING) {
            return redirect()->route('partner.pending');
        }

        if ($profile->status === PartnerStatus::SUSPENDED) {
            return redirect()->route('partner.suspended');
        }

        if ($profile->status === PartnerStatus::REJECTED) {
            abort(403, 'Your partner application has been rejected.');
        }

        return $next($request);
    }
}
