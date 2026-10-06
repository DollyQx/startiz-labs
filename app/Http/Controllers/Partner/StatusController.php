<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function pending(Request $request): View
    {
        $partner = $request->user()?->partnerProfile;

        return view('partner.pending', compact('partner'));
    }

    public function suspended(Request $request): View
    {
        $partner = $request->user()?->partnerProfile;

        return view('partner.suspended', compact('partner'));
    }
}
