<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Http\Resources\CourtResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CourtController extends Controller
{
    /**
     * Dashboard — list & search courts.
     */
    public function index(Request $request)
    {
        $query = Court::active();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('city', 'like', "%{$q}%")
                   ->orWhere('address', 'like', "%{$q}%");
            });
        }

        if ($request->filled('sport')) {
            $query->where('sport_type', $request->sport);
        }

        if ($request->filled('city')) {
            $query->byCity($request->city);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_hour', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }

        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sort
        match ($request->sort ?? 'rating') {
            'price-low'  => $query->orderBy('price_per_hour'),
            'price-high' => $query->orderByDesc('price_per_hour'),
            'name'       => $query->orderBy('name'),
            default      => $query->orderByDesc('rating'),
        };

        $courts = CourtResource::collection($query->get())->resolve();

        // User favorites
        $favoriteIds = Auth::check()
            ? Auth::user()->favorites()->pluck('courts.id')->toArray()
            : [];

        $filters = $request->only(['q', 'sport', 'city', 'min_price', 'max_price', 'min_rating', 'sort']);
        if (!isset($filters['sort'])) {
            $filters['sort'] = '';
        }

        return Inertia::render('Dashboard', [
            'courts'     => $courts,
            'favorites'  => $favoriteIds,
            'filters'    => $filters,
        ]);
    }

    /**
     * Court Detail page.
     */
    public function show(Court $court)
    {
        $isFav = Auth::check()
            ? Auth::user()->favorites()->where('courts.id', $court->id)->exists()
            : false;

        $today   = now()->toDateString();
        $maxDate = now()->copy()->addDays(30)->toDateString();

        $bookedSlots = \App\Models\Booking::bookedForCourt($court->id, $today, $maxDate)
            ->get(['date', 'time_start', 'time_end', 'court_detail', 'items'])
            ->toArray();

        // Ambil reviews terbaru (max 20)
        $reviews = $court->reviews()
            ->with('user')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($t) => [
                'id'         => $t->id,
                'name'       => $t->user->name ?? 'Guest',
                'avatar'     => $t->user->avatar ?? null,
                'comment'    => $t->comment,
                'rating'     => $t->rating,
                'created_at' => $t->created_at->diffForHumans(),
            ]);

        // Ambil peralatan sewa aktif
        $equipment = $court->equipment()
            ->get()
            ->map(fn ($e) => [
                'id'             => $e->id,
                'name'           => $e->name,
                'icon'           => $e->icon,
                'description'    => $e->description,
                'price_per_unit' => $e->price_per_unit,
            ]);

        return Inertia::render('CourtDetail', [
            'court'        => (new CourtResource($court))->resolve(),
            'isFav'        => $isFav,
            'bookedSlots'  => $bookedSlots,
            'testimonials' => $reviews,
            'equipment'    => $equipment,
        ]);
    }

    /**
     * Toggle favorite.
     */
    public function toggleFavorite(Request $request, Court $court)
    {
        $user = Auth::user();
        $toggled = $user->favorites()->toggle($court->id);

        $isFav = !empty($toggled['attached']);

        return response()->json([
            'isFav'   => $isFav,
            'message' => $isFav ? 'Ditambahkan ke favorit' : 'Dihapus dari favorit',
        ]);
    }

    /**
     * Favorites page.
     */
    public function favorites()
    {
        $courts = Auth::user()->favorites()
            ->get();
        $courts = CourtResource::collection($courts)->resolve();

        return Inertia::render('Favorites', ['courts' => $courts]);
    }

}
