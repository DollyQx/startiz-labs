<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IndustryController extends Controller
{
    public function index(): View
    {
        $industries = Industry::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('public.industries.index', compact('industries'));
    }

    public function show(string $slug): View
    {
        $industry = Industry::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$industry) {
            $industry = new Industry([
                'name' => Str::headline($slug),
                'slug' => $slug,
                'description' => 'Specialized software tools and operational platforms engineered specifically for industry workflows.',
            ]);
        }

        return view('public.industries.show', compact('industry'));
    }
}
