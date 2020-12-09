<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use Spatie\WebhookClient\Exceptions\WebhookFailed as SpatieWebhookFailed;

final class UnexpectedWebhookPayload extends SpatieWebhookFailed
{
    public static function missingEvent(): self
    {
        return new self('The webhook payload is missing an `event` property.');
    }
}
