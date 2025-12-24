<?php

namespace App\Http\Controllers;

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

        // In a real application, you would:
        // 1. Send an email notification
        // 2. Store the message in the database
        // 3. Send a confirmation email to the user

        // For now, we'll just return a success response
        return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }

    public function corporateProfile()
    {
        return Inertia::render('CorporateProfile');
    }
}
