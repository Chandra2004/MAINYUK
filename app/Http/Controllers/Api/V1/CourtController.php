<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Http\Resources\CourtResource;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    public function index(Request $request)
    {
        $courts = Court::active()->get();
        return CourtResource::collection($courts);
    }

    public function show(Court $court)
    {
        return new CourtResource($court);
    }
}
