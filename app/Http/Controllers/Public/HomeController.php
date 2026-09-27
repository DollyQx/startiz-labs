<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Offer;
use App\Models\Service;
use App\Services\PortfolioService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Render the clean, high-conversion public homepage for Startiz Labs.
     */
    public function index(): View
    {
        $services = Service::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $industries = Industry::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $portfolioProjects = PortfolioService::getProjects();
        $testimonials = PortfolioService::getTestimonials();

        $activeOffer = Offer::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->latest()
            ->first();

        return view('public.home', compact(
            'services',
            'industries',
            'portfolioProjects',
            'testimonials',
            'activeOffer'
        ));
    }
}
