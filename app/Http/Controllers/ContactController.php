<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function create()
    {
        $services = Service::published()->orderBy('title')->get(['id', 'title']);
        $selectedServiceId = old('service_id');
        if (! $selectedServiceId && request()->filled('service')) {
            $selectedServiceId = Service::published()
                ->where('slug', request()->query('service'))
                ->value('id');
        }

        return view('pages.contact', compact('services', 'selectedServiceId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'phone' => ['nullable', 'string', 'max:40'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'subject' => ['nullable', 'string', 'max:180', 'not_regex:/[\r\n]/'],
            'message' => ['required', 'string', 'max:5000'],
            'company_website' => ['nullable', 'size:0'],
        ]);

        unset($validated['company_website']);
        if (! empty($validated['service_id']) && ! Service::published()->whereKey($validated['service_id'])->exists()) {
            return back()->withErrors(['service_id' => 'Please choose a currently available service.'])->withInput();
        }
        $inquiry = ContactInquiry::create($validated);
        $inquiry->load('service');
        $recipient = SiteSetting::value('site_inquiries_email', config('site.inquiries_email'));
        $notificationFailed = false;

        if ($recipient && config('mail.default') !== 'log') {
            try {
                Mail::raw(
                    "Name: {$inquiry->name}\nEmail: {$inquiry->email}\nPhone: ".($inquiry->phone ?: 'Not provided')
                    ."\nService: ".($inquiry->service?->title ?: 'Not selected')
                    ."\nSubject: ".($inquiry->subject ?: 'Not provided')."\n\n{$inquiry->message}",
                    function ($message) use ($recipient, $inquiry) {
                        $message->to($recipient)->subject('Website inquiry: '.($inquiry->subject ?: $inquiry->name));
                        $message->replyTo($inquiry->email, $inquiry->name);
                    }
                );
            } catch (Throwable $exception) {
                Log::error('Contact inquiry notification could not be sent.', [
                    'inquiry_id' => $inquiry->id,
                    'exception' => $exception,
                ]);
                $notificationFailed = true;
            }
        }

        $response = redirect()->route('contact')->with('status', 'Thank you. Your inquiry has been received.');
        if ($notificationFailed) {
            $response->with('notification_warning', 'Your inquiry was saved, but the email notification could not be sent. Please contact us directly if your matter is urgent.');
        }

        return $response;
    }
}
