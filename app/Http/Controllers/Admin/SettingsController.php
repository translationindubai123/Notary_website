<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private const SETTINGS = [
        'site_name',
        'site_description',
        'home_hero_eyebrow',
        'home_hero_title',
        'home_hero_introduction',
        'home_meta_description',
        'home_primary_cta',
        'home_secondary_cta',
        'site_phone',
        'site_whatsapp',
        'site_email',
        'site_inquiries_email',
        'site_address',
        'site_office_hours',
    ];

    public function edit()
    {
        return view('admin.settings', [
            'settings' => SiteSetting::query()->pluck('setting_value', 'setting_key')->all(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:180'],
            'site_description' => ['nullable', 'string', 'max:320'],
            'home_hero_eyebrow' => ['required', 'string', 'max:120'],
            'home_hero_title' => ['required', 'string', 'max:180'],
            'home_hero_introduction' => ['required', 'string', 'max:500'],
            'home_meta_description' => ['required', 'string', 'max:320'],
            'home_primary_cta' => ['required', 'string', 'max:60'],
            'home_secondary_cta' => ['required', 'string', 'max:60'],
            'site_phone' => ['nullable', 'string', 'max:60'],
            'site_whatsapp' => [
                'nullable',
                'string',
                'max:40',
                'regex:/^[0-9+() .-]+$/',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $digits = preg_replace('/\D/', '', $value);
                        if (strlen($digits) < 8 || strlen($digits) > 15) {
                            $fail('The WhatsApp number must contain between 8 and 15 digits, including its country code.');
                        }
                    }
                },
            ],
            'site_email' => ['nullable', 'email', 'max:254'],
            'site_inquiries_email' => ['nullable', 'email', 'max:254'],
            'site_address' => ['nullable', 'string', 'max:1000'],
            'site_office_hours' => ['nullable', 'string', 'max:500'],
            'site_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_site_logo' => ['nullable', 'boolean'],
        ]);

        $previousLogo = SiteSetting::value('site_logo');
        $newLogo = $request->file('site_logo')?->store('site', 'public');
        $removeLogo = ! $newLogo && $request->boolean('remove_site_logo');

        DB::transaction(function () use ($validated, $newLogo, $removeLogo) {
            foreach (self::SETTINGS as $key) {
                SiteSetting::updateOrCreate(
                    ['setting_key' => $key],
                    ['setting_value' => $validated[$key] ?? null]
                );
            }
            if ($newLogo || $removeLogo) {
                SiteSetting::updateOrCreate(
                    ['setting_key' => 'site_logo'],
                    ['setting_value' => $newLogo]
                );
            }
        });

        if (($newLogo || $removeLogo) && $previousLogo) {
            Storage::disk('public')->delete($previousLogo);
        }
        Cache::forget('site-settings');

        return redirect()->route('admin.settings.edit')->with('status', 'Site settings updated.');
    }
}
