<?php

namespace Modules\Auth\Services;
use Illuminate\Support\Facades\Http;

class OtpService
{
    public function sendOTPViaWhatsApp($phone, $otp)
    {
        $authKey = config('services.msg91.auth_key');
        $namespace = config('services.msg91.whatsapp_namespace');
        $integrated_number = config('services.msg91.integrated_number');
        $template_name = config('services.msg91.template_name');
        $payload = [
            'integrated_number' =>$integrated_number,
            'content_type' => 'template',
            'payload' => [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                'template' => [
                    'name' => $template_name,
                    'language' => [
                        'code' => 'en',
                        'policy' => 'deterministic'
                    ],
                    'namespace' => $namespace,
                    'to_and_components' => [
                        [
                            'to' => [
                               $phone
                            ],
                            'components' => [
                                'body_1' => [
                                    'type' => 'text',
                                    'value' => $otp
                                ],
                                'button_1' => [
                                    'subtype' => 'url',
                                    'type' => 'text',
                                    'value' => $otp
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'authkey' => $authKey,
            'Content-Type' => 'application/json',
        ])->post('https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/', $payload);

        if ($response->successful()) {
            return true;
        }

        return false;
    }

}
