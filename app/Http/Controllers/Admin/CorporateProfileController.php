<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateProfileContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CorporateProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = CorporateProfileContent::orderBy('section_type')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('section_type');

        return Inertia::render('Admin/CorporateProfile/Index', [
            'contents' => $contents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CorporateProfile/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle bulk office location fields
        if ($request->has('fields') && is_array($request->fields)) {
            $baseData = $request->validate([
                'section_type' => 'required|string|in:office_location',
                'key' => 'required|string|max:255',
                'title' => 'required|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $fields = $request->validate([
                'fields' => 'required|array|min:1',
                'fields.*.label' => 'required|string|max:255',
                'fields.*.value' => 'required|string',
                'fields.*.field_type' => 'required|string|max:50',
            ])['fields'];

            $baseData['is_active'] = filter_var($baseData['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $baseData['sort_order'] = $baseData['sort_order'] ?? 0;

            // Create multiple entries
            foreach ($fields as $index => $field) {
                CorporateProfileContent::create([
                    'section_type' => $baseData['section_type'],
                    'key' => $baseData['key'],
                    'title' => $baseData['title'],
                    'label' => $field['label'],
                    'value' => $field['value'],
                    'field_type' => $field['field_type'],
                    'sort_order' => $baseData['sort_order'] + $index,
                    'is_active' => $baseData['is_active'],
                ]);
            }

            return redirect()->route('admin.corporate-profile.index')
                ->with('success', count($fields) . ' contact fields created successfully!');
        }

        // Regular single entry
        $validated = $request->validate([
            'section_type' => 'required|string|in:page_title,company_info,office_location,contact_info',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'label' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'value' => 'nullable|string',
            'field_type' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Set defaults and convert boolean
        $validated['is_active'] = filter_var($validated['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        CorporateProfileContent::create($validated);

        return redirect()->route('admin.corporate-profile.index')
            ->with('success', 'Content created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $corporateProfile)
    {
        $content = CorporateProfileContent::findOrFail($corporateProfile);
        
        // If it's an office_location, load all entries with the same key
        $relatedContents = [];
        if ($content->section_type === 'office_location' && $content->key) {
            $relatedContents = CorporateProfileContent::where('section_type', 'office_location')
                ->where('key', $content->key)
                ->orderBy('sort_order')
                ->get();
        }
        
        return Inertia::render('Admin/CorporateProfile/Edit', [
            'content' => $content,
            'relatedContents' => $relatedContents,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $corporateProfile)
    {
        $content = CorporateProfileContent::findOrFail($corporateProfile);
        
        // Handle bulk office location fields update
        if ($request->has('fields') && is_array($request->fields)) {
            $baseData = $request->validate([
                'section_type' => 'required|string|in:office_location',
                'key' => 'required|string|max:255',
                'title' => 'required|string|max:500',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
                'delete_ids' => 'nullable|array',
                'delete_ids.*' => 'integer|exists:corporate_profile_contents,id',
            ]);

            $fields = $request->validate([
                'fields' => 'required|array|min:1',
                'fields.*.id' => 'nullable|integer|exists:corporate_profile_contents,id',
                'fields.*.label' => 'required|string|max:255',
                'fields.*.value' => 'required|string',
                'fields.*.field_type' => 'required|string|max:50',
            ])['fields'];

            $baseData['is_active'] = $baseData['is_active'] ?? true;
            $baseData['sort_order'] = $baseData['sort_order'] ?? 0;

            // Delete removed entries
            if (isset($baseData['delete_ids']) && is_array($baseData['delete_ids'])) {
                CorporateProfileContent::whereIn('id', $baseData['delete_ids'])->delete();
            }

            // Update or create entries
            foreach ($fields as $index => $field) {
                if (isset($field['id']) && $field['id']) {
                    // Update existing
                    CorporateProfileContent::where('id', $field['id'])->update([
                        'section_type' => $baseData['section_type'],
                        'key' => $baseData['key'],
                        'title' => $baseData['title'],
                        'label' => $field['label'],
                        'value' => $field['value'],
                        'field_type' => $field['field_type'],
                        'sort_order' => $baseData['sort_order'] + $index,
                        'is_active' => $baseData['is_active'],
                    ]);
                } else {
                    // Create new
                    CorporateProfileContent::create([
                        'section_type' => $baseData['section_type'],
                        'key' => $baseData['key'],
                        'title' => $baseData['title'],
                        'label' => $field['label'],
                        'value' => $field['value'],
                        'field_type' => $field['field_type'],
                        'sort_order' => $baseData['sort_order'] + $index,
                        'is_active' => $baseData['is_active'],
                    ]);
                }
            }

            return redirect()->route('admin.corporate-profile.index')
                ->with('success', 'Office location updated successfully!');
        }

        // Regular single entry update
        $validated = $request->validate([
            'section_type' => 'required|string|in:page_title,company_info,office_location,contact_info',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'label' => 'nullable|string|max:255',
            'text' => 'nullable|string',
            'value' => 'nullable|string',
            'field_type' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $content->update($validated);

        return redirect()->route('admin.corporate-profile.index')
            ->with('success', 'Content updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $corporateProfile)
    {
        $content = CorporateProfileContent::findOrFail($corporateProfile);
        $content->delete();

        return redirect()->route('admin.corporate-profile.index')
            ->with('success', 'Content deleted successfully!');
    }
}
