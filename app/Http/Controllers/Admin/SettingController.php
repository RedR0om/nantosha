<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $multiProductMode = Setting::isEnabled('multi_product_mode');

        return Inertia::render('Admin/Settings/Index', [
            'multiProductMode' => $multiProductMode,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'multi_product_mode' => 'required|boolean',
        ]);

        Setting::set('multi_product_mode', $request->multi_product_mode ? '1' : '0');

        return back()->with('success', 'Settings updated successfully!');
    }
}
