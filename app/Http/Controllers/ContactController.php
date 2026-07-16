<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        ContactMessage::create($validated);

        \Illuminate\Support\Facades\Mail::raw("Nama: {$validated['name']}\nEmail: {$validated['email']}\n\nPesan:\n{$validated['message']}", function ($mail) use ($validated) {
            $mail->to('admin@mainyuk.com')
                 ->subject('Pesan Kontak Baru dari ' . $validated['name']);
        });

        return back()->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}
