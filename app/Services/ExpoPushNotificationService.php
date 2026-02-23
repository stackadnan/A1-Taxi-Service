<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\DriverNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushNotificationService
{
    /**
     * Expo Push API endpoint
     */
    const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Send a push notification for a DriverNotification record.
     * Looks up the driver's expo_push_token and sends via Expo Push API.
     */
    public static function sendForNotification(DriverNotification $notification): bool
    {
        try {
            $driver = Driver::find($notification->driver_id);

            if (!$driver || !$driver->expo_push_token) {
                Log::info('ExpoPushNotificationService: no push token for driver', [
                    'driver_id' => $notification->driver_id,
                ]);
                return false;
            }

            return self::sendPush(
                $driver->expo_push_token,
                $notification->title,
                $notification->message,
                [
                    'notification_id' => $notification->id,
                    'type' => 'driver_notification',
                ]
            );
        } catch (\Exception $e) {
            Log::error('ExpoPushNotificationService: failed to send', [
                'driver_id' => $notification->driver_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a push notification to an Expo push token.
     *
     * @param string $token   ExponentPushToken[xxx]
     * @param string $title   Notification title
     * @param string $body    Notification body text
     * @param array  $data    Extra data payload
     * @return bool
     */
    public static function sendPush(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            // Validate token format
            if (!str_starts_with($token, 'ExponentPushToken[') && !str_starts_with($token, 'ExpoPushToken[')) {
                Log::warning('ExpoPushNotificationService: invalid token format', ['token' => $token]);
                return false;
            }

            $payload = [
                'to' => $token,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sound' => 'default',
                'priority' => 'high',
                'channelId' => 'driver-notifications',
            ];

            Log::info('ExpoPushNotificationService: sending push', [
                'token' => substr($token, 0, 30) . '...',
                'title' => $title,
            ]);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
            ])->post(self::EXPO_PUSH_URL, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $status = $responseData['data']['status'] ?? 'unknown';

                if ($status === 'ok') {
                    Log::info('ExpoPushNotificationService: push sent successfully', [
                        'token' => substr($token, 0, 30) . '...',
                    ]);
                    return true;
                }

                // Handle specific error types
                $errorMessage = $responseData['data']['message'] ?? '';
                $errorDetails = $responseData['data']['details'] ?? [];

                if (($responseData['data']['status'] ?? '') === 'error') {
                    $errorType = $errorDetails['error'] ?? '';

                    // If token is invalid, clear it from the driver record
                    if (in_array($errorType, ['DeviceNotRegistered', 'InvalidCredentials'])) {
                        Log::warning('ExpoPushNotificationService: invalid token, clearing', [
                            'token' => substr($token, 0, 30) . '...',
                            'error' => $errorType,
                        ]);
                        Driver::where('expo_push_token', $token)->update(['expo_push_token' => null]);
                    }
                }

                Log::warning('ExpoPushNotificationService: push failed', [
                    'status' => $status,
                    'message' => $errorMessage,
                ]);
                return false;
            }

            Log::error('ExpoPushNotificationService: HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('ExpoPushNotificationService: exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send push notifications to multiple tokens at once (batch).
     *
     * @param array  $tokens  Array of ExponentPushToken strings
     * @param string $title
     * @param string $body
     * @param array  $data
     * @return array  Results per token
     */
    public static function sendPushBatch(array $tokens, string $title, string $body, array $data = []): array
    {
        $messages = [];
        foreach ($tokens as $token) {
            if (str_starts_with($token, 'ExponentPushToken[') || str_starts_with($token, 'ExpoPushToken[')) {
                $messages[] = [
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                    'sound' => 'default',
                    'priority' => 'high',
                    'channelId' => 'driver-notifications',
                ];
            }
        }

        if (empty($messages)) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
            ])->post(self::EXPO_PUSH_URL, $messages);

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('ExpoPushNotificationService: batch HTTP error', [
                'status' => $response->status(),
            ]);
            return [];

        } catch (\Exception $e) {
            Log::error('ExpoPushNotificationService: batch exception', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
