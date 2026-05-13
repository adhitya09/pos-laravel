<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display setting page
     */
    public function index()
    {
        $setting = Setting::first();
        return view('pages.setting.index', compact('setting'));
    }

    /**
     * Update setting
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'print_type' => 'required|in:kabel,bluetooth',
            'printer_name' => 'nullable|string|max:255',
            'store_logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'store_name',
            'store_address',
            'store_phone',
            'print_type',
            'printer_name',
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            $setting = Setting::first();
            if ($setting && $setting->store_logo) {
                Storage::disk('public')->delete($setting->store_logo);
            }
            $data['store_logo'] = $request->file('store_logo')->store('settings', 'public');
        }

        Setting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Setting berhasil disimpan');
    }
}
