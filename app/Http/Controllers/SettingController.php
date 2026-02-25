<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the company information form (admin only).
     */
    public function company()
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $settings = [
            'company_name' => Setting::get('company_name', ''),
            'company_logo' => Setting::get('company_logo', ''),
            'company_registration_no' => Setting::get('company_registration_no', ''),
            'company_address' => Setting::get('company_address', ''),
            'company_phone' => Setting::get('company_phone', ''),
            'company_website' => Setting::get('company_website', ''),
            'company_email' => Setting::get('company_email', ''),
        ];

        return view('settings.company', compact('settings'));
    }

    /**
     * Update company information.
     */
    public function updateCompany(Request $request)
    {
        $this->authorize('viewAny', \App\Models\User::class);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,gif,svg,webp|max:2048',
            'company_registration_no' => 'nullable|string|max:100',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'company_email' => 'nullable|email|max:255',
        ]);

        Setting::set('company_name', $validated['company_name']);
        Setting::set('company_registration_no', $validated['company_registration_no'] ?? '');
        Setting::set('company_address', $validated['company_address'] ?? '');
        Setting::set('company_phone', $validated['company_phone'] ?? '');
        Setting::set('company_website', $validated['company_website'] ?? '');
        Setting::set('company_email', $validated['company_email'] ?? '');

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $dir = 'images';
            $name = 'logo.' . $file->getClientOriginalExtension();
            $file->move(public_path($dir), $name);
            $path = $dir . '/' . $name;
            Setting::set('company_logo', $path);
        }

        return redirect()->route('settings.company')
            ->with('success', 'Company information updated successfully.');
    }
}
