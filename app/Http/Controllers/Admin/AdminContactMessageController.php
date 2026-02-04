<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Inertia\Inertia;

class AdminContactMessageController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ContactMessages/Index', [
            'messages' => ContactMessage::latest()->get(),
            'unreadCount' => ContactMessage::where('is_read', false)->count(),
        ]);
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return Inertia::render('Admin/ContactMessages/Show', [
            'message' => $message,
        ]);
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted.');
    }
}
