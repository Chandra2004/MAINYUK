<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message using a provider (e.g. Fonnte or WATS).
     * This uses Fonnte API as an example, but can be adapted to others.
     *
     * @param string $phone   The target phone number
     * @param string $message The message body
     * @return bool
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $apiKey = config('services.whatsapp.api_key');
        $endpoint = config('services.whatsapp.endpoint', 'https://api.fonnte.com/send');

        if (!$apiKey) {
            Log::warning('WhatsAppService: API Key is not configured. Message to ' . $phone . ' not sent.');
            // Return true in local environment to simulate success
            return app()->environment('local');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey
            ])->post($endpoint, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent to ' . $phone);
                return true;
            }

            Log::error('WhatsApp message failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsAppService exception: ' . $e->getMessage());
            return false;
        }
    }
}
