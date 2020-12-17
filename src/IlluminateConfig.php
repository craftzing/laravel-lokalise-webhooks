<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\AppMisconfigured;
use Illuminate\Contracts\Config\Repository;

final class IlluminateConfig implements Config
{
    private string $lokaliseXSecret;
    private bool $areIpRestrictionsEnabled;

    public function __construct(Repository $config)
    {
        $this->lokaliseXSecret = $this->lokaliseSecretFromConfig($config);
        $this->areIpRestrictionsEnabled = $config->get('lokalise-webhooks.enable_ip_restrictions');
    }

    private function lokaliseSecretFromConfig(Repository $config): string
    {
        if ($value = $config->get('lokalise-webhooks.x_secret')) {
            return $value;
        }

        throw AppMisconfigured::missingLokaliseXSecret();
    }

    public function lokaliseXSecret(): string
    {
        return $this->lokaliseXSecret;
    }

    public function areIpRestrictionsEnabled(): bool
    {
        return $this->areIpRestrictionsEnabled;
    }
}
