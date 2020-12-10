<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

final class LokaliseSignatureValidator implements SignatureValidator
{
    public const NAME = 'lokalise';
    public const SECRET_HEADER_NAME = 'X-Secret';

    public function isValid(Request $request, WebhookConfig $config): bool
    {
        return $request->header(self::SECRET_HEADER_NAME) === $config->signingSecret;
    }
}
