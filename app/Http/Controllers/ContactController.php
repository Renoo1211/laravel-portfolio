<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        // Honeypot: bot mengisi field "website", manusia tidak melihatnya.
        if ($request->filled('website')) {
            return response()->json(['ok' => true]);
        }

        Mail::to(config('portfolio.contact_email'))->send(new ContactMessage($request->validated()));

        return response()->json(['ok' => true]);
    }
}
