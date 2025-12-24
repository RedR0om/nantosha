<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Store the inquiry in the database
        Inquiry::create($validated);

        return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }

    public function drLandritoProfile()
    {
        return Inertia::render('DrLandritoProfile');
    }

    public function corporateProfile()
    {
        return Inertia::render('CorporateProfile');
    }
}
