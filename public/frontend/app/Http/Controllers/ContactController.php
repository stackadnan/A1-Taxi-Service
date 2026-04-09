<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request): Response
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = config('mail.from.address', 'example@example.com');
        $subject = trim((string) ($data['subject'] ?? 'Website contact form'));

        $body = "Name: {$data['name']}\n"
            . "Email: {$data['email']}\n"
            . "Phone: " . ($data['phone'] ?? '') . "\n\n"
            . "Message:\n{$data['message']}\n";

        try {
            Mail::raw($body, function ($message) use ($recipient, $subject, $data): void {
                $message
                    ->to($recipient)
                    ->subject($subject)
                    ->replyTo($data['email'], $data['name']);
            });

            return response('Thanks for contacting us. We will contact you ASAP!', 200);
        } catch (\Throwable $e) {
            Log::error('Contact form email failed', [
                'error' => $e->getMessage(),
                'email' => $data['email'],
            ]);

            return response('Oops! Something went wrong and we could not send your message.', 500);
        }
    }
}
