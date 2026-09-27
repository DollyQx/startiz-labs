<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('public.services.index', compact('services'));
    }

    public function show(string $slug): View
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$service) {
            $service = new Service([
                'name' => Str::headline($slug),
                'slug' => $slug,
                'short_description' => 'Custom digital engineering service engineered with enterprise-grade architecture and clean code.',
                'description' => 'We specialize in engineering robust digital systems. Our development lifecycle emphasizes data isolation, secure authentication, financial precision, and high-performance server execution.',
            ]);
        }

        return view('public.services.show', compact('service'));
    }
}
