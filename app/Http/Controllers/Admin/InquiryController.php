<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::orderBy('created_at', 'desc');

        // Filtering
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->input('is_read') === 'true');
        }

        $inquiries = $query->paginate(20);

        // Stats
        $totalInquiries = Inquiry::count();
        $unreadInquiries = Inquiry::where('is_read', false)->count();
        $readInquiries = Inquiry::where('is_read', true)->count();

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries' => $inquiries,
            'stats' => [
                'total_inquiries' => $totalInquiries,
                'unread_inquiries' => $unreadInquiries,
                'read_inquiries' => $readInquiries,
            ],
            'filters' => $request->only(['search', 'is_read']),
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        // Mark as read when viewing
        if (!$inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }

        return Inertia::render('Admin/Inquiries/Show', [
            'inquiry' => $inquiry,
        ]);
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'is_read' => 'boolean',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry updated successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}
