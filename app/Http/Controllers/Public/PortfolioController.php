<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $projects = PortfolioService::getProjects();

        return view('public.portfolio.index', compact('projects'));
    }

    public function show(string $slug): View
    {
        $project = PortfolioService::findProject($slug);

        return view('public.portfolio.show', compact('slug', 'project'));
    }
}
