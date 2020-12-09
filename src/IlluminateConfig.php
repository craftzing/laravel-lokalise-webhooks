<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\AppMisconfigured;
use Illuminate\Contracts\Config\Repository;

final class IlluminateConfig implements Config
{
    private string $lokaliseXSecret;

    public function __construct(Repository $config)
    {
        $this->lokaliseXSecret = $config->get('lokalize-webhooks.x_secret') ?: '';

        $this->guardAgainstMissingLokaliseXSecret();
    }

    private function guardAgainstMissingLokaliseXSecret(): void
    {
        if (! $this->lokaliseXSecret) {
            throw AppMisconfigured::missingLokaliseXSecret();
        }
    }

    public function lokaliseXSecret(): string
    {
        return $this->lokaliseXSecret;
    }
}
