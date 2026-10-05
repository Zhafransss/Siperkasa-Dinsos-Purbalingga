<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /** Four cards per row on desktop, two full rows per page. */
    private const PER_PAGE = 8;

    public function index(): View
    {
        return view('katalog', [
            'vehicles' => Vehicle::query()->with('photos')->orderBy('id')->paginate(self::PER_PAGE),
        ]);
    }
}
