<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'vehicles' => Vehicle::query()->with('photos')->orderBy('id')->limit(3)->get(),
        ]);
    }
}
