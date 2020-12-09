<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use Exception;

final class AppMisconfigured extends Exception
{
    public static function missingLokaliseXSecret(): self
    {
        return new self(
            'Please make sure to provide a Lokalise X-Secret by either setting the ' .
            '`lokalise-webhooks.x_secret` config or the according environment variable.'
        );
    }
}
