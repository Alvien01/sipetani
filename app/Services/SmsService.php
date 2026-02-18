<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $provider;
    protected $apiKey;
    protected $apiSecret;
    protected $fromNumber;

    public function __construct()
    {
        $this->provider = config('services.sms.provider', 'websms');
        $this->apiKey = config('services.sms.api_key');
        $this->apiSecret = config('services.sms.api_secret');
        $this->fromNumber = config('services.sms.from_number');
    }

    public function send($phone, $message)
    {
        try {
            switch ($this->provider) {
                case 'twilio':
                    return $this->sendViaTwilio($phone, $message);
                case 'websms':
                    return $this->sendViaWebSMS($phone, $message);
                default:
                    Log::error("Unknown SMS provider: {$this->provider}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS Send Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $phones, $message)
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        return $results;
    }

    protected function sendViaTwilio($phone, $message)
    {
        $accountSid = $this->apiKey;
        $authToken = $this->apiSecret;
        $fromNumber = $this->fromNumber;

        $formattedPhone = $this->formatPhoneNumberInternational($phone);

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $response = Http::asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->post($url, [
                'From' => $fromNumber,
                'To' => $formattedPhone,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            $result = $response->json();
            Log::info("SMS sent to {$phone} via Twilio", [
                'sid' => $result['sid'] ?? null,
                'status' => $result['status'] ?? null
            ]);
            return true;
        }

        Log::error("Twilio SMS failed", [
            'phone' => $phone,
            'status' => $response->status(),
            'response' => $response->body()
        ]);
        return false;
    }

    protected function sendViaWebSMS($phone, $message)
    {
        $url = config('services.sms.websms_endpoint', 'https://websms.co.id/api/smsgateway');

        $formattedPhone = $this->formatPhoneNumber($phone);

        $url = $url . '?token=' . $this->apiKey .'&to=' . $formattedPhone .'&msg=' . urlencode($message);

        $response = Http::get($url);

        if ($response->successful()) {
            $result = $response->json();
            Log::info("SMS sent to {$phone} via WebSMS", [
                'payload' => $url,
                'response' => $result
            ]);

            if (isset($result['status']) && $result['status'] === 'success') {
                Log::info("SMS sent to {$phone} via WebSMS", [
                    'formatted_phone' => $formattedPhone,
                ]);
                return true;
            }

            Log::error("WebSMS failed", [
                'phone' => $phone,
                'response' => $result
            ]);
            return false;
        }

        Log::error("WebSMS HTTP failed: " . $response->body());
        return false;
    }

    protected function formatPhoneNumber($phone)
    {
        $phone = str_replace([' ', '-', '(', ')'], '', $phone);
        $phone = preg_replace('/^\+62/', '0', $phone);
        $phone = preg_replace('/^62/', '0', $phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    protected function formatPhoneNumberInternational($phone)
    {
        $phone = str_replace([' ', '-', '(', ')'], '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+62' . $phone;
        }

        return $phone;
    }
}
