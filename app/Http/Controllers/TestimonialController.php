<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
{
    public function store(Request $request, $courtId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Testimonial::create([
            'court_id' => $courtId,
            'user_id'  => Auth::id(),
            'name'     => Auth::user()->name,
            'avatar'   => Auth::user()->avatar,
            'rating'   => $request->rating,
            'comment'  => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
