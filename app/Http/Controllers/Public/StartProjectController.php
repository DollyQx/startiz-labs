<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StartProjectController extends Controller
{
    public function index(): View
    {
        return view('public.start-project');
    }
}
