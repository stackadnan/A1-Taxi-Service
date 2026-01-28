<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationStreamController extends Controller
{
    /**
     * Server-Sent Events stream for admin notifications
     */
    public function stream(Request $request)
    {
        // Diagnostic log: capture incoming SSE requests and whether a session cookie is present
        \Log::info('AdminNotificationStreamController: request received', [
            'path' => $request->path(), 
            'accept' => $request->header('Accept'),
            'has_session_cookie' => $request->hasCookie(session()->getName())
        ]);

        $user = auth()->user();
        if (!$user) {
            \Log::warning('AdminNotificationStreamController: rejecting unauthorized SSE request', [
                'has_session_cookie' => $request->hasCookie(session()->getName())
            ]);
            
            // Return 401 with proper headers for SSE
            return response('Unauthorized', 401)->header('Content-Type', 'text/event-stream');
        }

        \Log::info('AdminNotificationStreamController: connection started', [
            'user_id' => $user->id, 
            'lastId' => $request->header('Last-Event-ID', 0)
        ]);

        $response = new StreamedResponse(function () use ($user, $request) {
            // Set timeout to 55 seconds (just under typical proxy timeout)
            set_time_limit(60);
            
            $lastId = request()->header('Last-Event-ID', 0);
            \Log::info('AdminNotificationStreamController: streaming loop started', ['user_id' => $user->id, 'lastId' => $lastId]);
            $startTime = time();
            $timeout = 55; // 55 seconds
            
            while (time() - $startTime < $timeout) {
                // Check for new notifications
                $notifications = UserNotification::where('user_id', $user->id)
                    ->where('id', '>', $lastId)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->get();

                if ($notifications->count() > 0) {
                    \Log::info('AdminNotificationStreamController: found new notifications', ['user_id' => $user->id, 'count' => $notifications->count(), 'lastId' => $lastId]);
                    foreach ($notifications as $notification) {
                        \Log::info('AdminNotificationStreamController: sending notification', ['user_id' => $user->id, 'notification_id' => $notification->id]);
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

                        // Mark notification as delivered so it is not re-sent on reconnects
                        try {
                            $notification->is_read = true;
                            $notification->read_at = now();
                            $notification->save();
                            \Log::info('AdminNotificationStreamController: marked as delivered', ['user_id' => $user->id, 'notification_id' => $notification->id]);
                        } catch (\Exception $e) {
                            \Log::warning('AdminNotificationStreamController: failed to mark delivered', ['error' => $e->getMessage(), 'notification_id' => $notification->id]);
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
