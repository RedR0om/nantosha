<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomePageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomePageSectionController extends Controller
{
    public function index()
    {
        $sections = HomePageSection::orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/HomePageSections/Index', [
            'sections' => $sections,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HomePageSections/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:hero,features,product_info,pricing,how_it_works,custom,carousel,best_sellers,new_arrivals,customer_favorites,featured_product',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|array',
            'background_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Set defaults
        $validated['background_color'] = $validated['background_color'] ?? 'white';
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        HomePageSection::create($validated);

        return redirect()->route('admin.homepage-sections.index')
            ->with('success', 'Homepage section created successfully!');
    }

    public function edit(HomePageSection $homepageSection)
    {
        return Inertia::render('Admin/HomePageSections/Edit', [
            'section' => $homepageSection,
        ]);
    }

    public function update(Request $request, HomePageSection $homepageSection)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:hero,features,product_info,pricing,how_it_works,custom,carousel,best_sellers,new_arrivals,customer_favorites,featured_product',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|array',
            'background_color' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['background_color'] = $validated['background_color'] ?? 'white';

        $homepageSection->update($validated);

        return redirect()->route('admin.homepage-sections.index')
            ->with('success', 'Homepage section updated successfully!');
    }

    public function destroy(HomePageSection $homepageSection)
    {
        $homepageSection->delete();

        return redirect()->route('admin.homepage-sections.index')
            ->with('success', 'Homepage section deleted successfully!');
    }
}