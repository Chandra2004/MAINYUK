<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Court;
use App\Http\Resources\CourtResource;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Gunakan format yang sama dengan CourtController::formatCourt()
        // agar data konsisten antara halaman Home dan Dashboard
        $courts = CourtResource::collection(Court::active()->get())->resolve();

        $favoriteIds = Auth::check()
            ? Auth::user()->favorites()->pluck('courts.id')->toArray()
            : [];

        $promos = \App\Models\Promo::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>', now());
            })
            ->get(['code', 'discount_percent', 'description']);

        return Inertia::render('Home', [
            'courts'    => $courts,
            'favorites' => $favoriteIds,
            'dbPromos'  => $promos,
        ]);
    }
}
