<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use Illuminate\Http\Request;

class AdminBroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = Broadcast::query()
            ->where(function ($q) {
                $q->where('channel', 'admin_panel')
                    ->orWhereNull('channel');
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.admin-broadcasts.index', compact('broadcasts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        Broadcast::create([
            'created_by' => auth()->id(),
            'title' => trim($data['title']),
            'message' => trim($data['message']),
            'channel' => 'admin_panel',
            'scheduled_at' => $data['scheduled_at'] ?? now(),
            'sent_at' => now(),
            'target' => [
                'surface' => 'admin_panel',
            ],
        ]);

        return redirect()
            ->route('admin.admin-broadcasts.index')
            ->with('success', 'Admin broadcast message saved successfully.');
    }
}
