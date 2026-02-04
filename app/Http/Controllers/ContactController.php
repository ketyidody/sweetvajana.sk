<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function show()
    {
        return Inertia::render('Contact', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'recaptcha_token' => 'required|string',
        ]);

        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $validated['recaptcha_token'],
            'remoteip' => $request->ip(),
        ]);

        $recaptchaData = $recaptchaResponse->json();

        if (! ($recaptchaData['success'] ?? false) || ($recaptchaData['score'] ?? 0) < 0.5) {
            return back()->withErrors(['recaptcha_token' => 'reCAPTCHA verification failed. Please try again.']);
        }

        ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        $to = SiteSetting::get('email', config('mail.from.address'));

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\n\n{$validated['message']}",
            function ($message) use ($validated, $to) {
                $message->to($to)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject("Contact: {$validated['subject']}");
            }
        );

        return redirect()->back()->with('success', 'Your message has been sent. We will get back to you soon!');
    }
}
