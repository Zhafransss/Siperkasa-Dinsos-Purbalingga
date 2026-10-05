<?php

namespace App\Http\Controllers;

use App\Services\PlaceSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function search(Request $request, PlaceSearch $places): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'max:100']]);

        return response()->json(['places' => $places->search($data['q'])]);
    }
}
