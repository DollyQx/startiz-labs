<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $partner = $request->user()->partnerProfile;
        $referralUrl = $partner->referralUrl();

        return view('partner.links', compact('partner', 'referralUrl'));
    }
}
