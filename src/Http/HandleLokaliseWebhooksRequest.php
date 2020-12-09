<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Craftzing\Laravel\LokaliseWebhooks\Config;
use Craftzing\Laravel\LokaliseWebhooks\LokaliseSignatureValidator;
use Craftzing\Laravel\LokaliseWebhooks\ProcessLokaliseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookProfile\ProcessEverythingWebhookProfile;

final class HandleLokaliseWebhooksRequest
{
    public function __invoke(Request $request, Config $config): JsonResponse
    {
        $webhookConfig = new WebhookConfig([
            'name' => LokaliseSignatureValidator::NAME,
            'signing_secret' => $config->lokaliseXSecret(),
            'signature_header_name' => LokaliseSignatureValidator::SECRET_HEADER_NAME,
            'signature_validator' => LokaliseSignatureValidator::class,
            'webhook_profile' => ProcessEverythingWebhookProfile::class,
            'webhook_model' => WebhookCall::class,
            'process_webhook_job' => ProcessLokaliseWebhook::class,
        ]);

        (new WebhookProcessor($request, $webhookConfig))->process();

        return response()->json(['message' => 'ok']);
    }
}
