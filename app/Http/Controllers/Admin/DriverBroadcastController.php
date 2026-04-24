<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverBroadcast;
use App\Models\DriverNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DriverBroadcastController extends Controller
{
    public function index()
    {
        $broadcasts = DriverBroadcast::query()
            ->with('creator:id,name')
            ->latest()
            ->paginate(15);

        $driverCount = Driver::count();
        $councils = DB::table('councils')->where('status', 'active')->orderBy('council_name')->get();

        return view('admin.driver-broadcasts.index', compact('broadcasts', 'driverCount', 'councils'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
            'broadcast_type' => ['nullable', 'string', 'max:50'],
            'council_id' => ['nullable', 'integer'],
        ]);

        $broadcast = DriverBroadcast::create([
            'title' => trim($data['title']),
            'message' => trim($data['message']),
            'broadcast_type' => $data['broadcast_type'] ?: 'general',
            'status' => 'sent',
            'scheduled_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if (!empty($data['council_id'])) {
            DB::table('driver_broadcast_councils')->insert([
                'broadcast_id' => $broadcast->id,
                'council_id' => $data['council_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $canWriteLogs = Schema::hasTable('driver_broadcast_logs');
        $sentCount = 0;

        $query = Driver::query()->select('id', 'council_id')->orderBy('id');
        if (!empty($data['council_id'])) {
            $query->where('council_id', $data['council_id']);
        }

        $query->chunkById(200, function ($drivers) use ($broadcast, $canWriteLogs, &$sentCount) {
            $logRows = [];
                $now = now();

                foreach ($drivers as $driver) {
                    DriverNotification::create([
                        'driver_id' => $driver->id,
                        'title' => $broadcast->title,
                        'message' => $broadcast->message,
                    ]);

                    $sentCount++;

                    if ($canWriteLogs) {
                        $logRows[] = [
                            'broadcast_id' => $broadcast->id,
                            'driver_id' => $driver->id,
                            'council_id' => $driver->council_id,
                            'channel_type' => 'push',
                            'recipient_contact' => null,
                            'status' => 'sent',
                            'error_message' => null,
                            'sent_at' => $now,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($canWriteLogs && !empty($logRows)) {
                    DB::table('driver_broadcast_logs')->insert($logRows);
                }
            });

        return redirect()
            ->route('admin.driver-broadcasts.index')
            ->with('success', "Broadcast sent to {$sentCount} driver(s).")
            ->with('broadcast_created_id', $broadcast->id);
    }
}
