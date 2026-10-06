<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    public function index(Request $request): View
    {
        $partner = $request->user()->partnerProfile;

        $period = $request->query('period', 'all');
        if (! in_array($period, ['all', 'month', 'year'], true)) {
            $period = 'all';
        }

        $leaderboard = $this->leaderboardService->getLeaderboard($period);
        $myRank = $this->leaderboardService->getPartnerRank($partner, $period);
        $myEntry = $leaderboard->firstWhere('partner_id', $partner->id);

        return view('partner.leaderboard', compact('partner', 'leaderboard', 'myRank', 'myEntry', 'period'));
    }
}
