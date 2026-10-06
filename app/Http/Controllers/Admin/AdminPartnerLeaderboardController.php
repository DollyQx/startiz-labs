<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnerLeaderboardController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    public function index(Request $request): View
    {
        $period = $request->query('period', 'all');
        if (! in_array($period, ['all', 'month', 'year'], true)) {
            $period = 'all';
        }

        $leaderboard = $this->leaderboardService->getLeaderboard($period);

        return view('admin.partners.leaderboard', compact('leaderboard', 'period'));
    }
}
