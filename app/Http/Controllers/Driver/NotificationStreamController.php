<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationStreamController extends Controller
{
    /**
     * Server-Sent Events stream for driver notifications
     */
    public function stream(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            abort(403);
        }

        $response = new StreamedResponse(function () use ($driver) {
            // Set timeout to 55 seconds (just under typical proxy timeout)
            set_time_limit(60);
            
            $lastId = request()->header('Last-Event-ID', 0);
            \Log::info('DriverNotificationStreamController: connection started', ['driver_id' => $driver->id, 'lastId' => $lastId]);
            $startTime = time();
            $timeout = 55; // 55 seconds
            
            while (time() - $startTime < $timeout) {
                // Check for new notifications
                $notifications = DriverNotification::where('driver_id', $driver->id)
                    ->where('id', '>', $lastId)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($notifications->count() > 0) {
                    foreach ($notifications as $notification) {
                        echo "id: {$notification->id}\n";
                        echo "event: notification\n";
                        echo 'data: ' . json_encode([
                            'id' => $notification->id,
                            'title' => $notification->title,
                            'message' => $notification->message,
                            'created_at' => $notification->created_at->toIso8601String()
                        ]) . "\n\n";
                        ob_flush();
                        flush();
                        $lastId = $notification->id;

                        // Mark as delivered so this won't be re-sent
                        try {
                            $notification->is_read = true;
                            $notification->read_at = now();
                            $notification->save();
                            \Log::info('DriverNotificationStreamController: marked delivered', ['driver_id' => $driver->id, 'notification_id' => $notification->id]);
                        } catch (\Exception $e) {
                            \Log::warning('DriverNotificationStreamController: failed to mark delivered', ['error' => $e->getMessage(), 'notification_id' => $notification->id]);
                        }
                    }
                }

                // Send heartbeat to keep connection alive
                echo ": heartbeat\n\n";
                ob_flush();
                flush();

                // Wait 2 seconds before next check
                sleep(2);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no'); // Disable nginx buffering

        return $response;
    }
}
