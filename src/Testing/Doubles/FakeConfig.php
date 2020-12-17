<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Testing\Doubles;

use Craftzing\Laravel\LokaliseWebhooks\Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

use function tap;

/**
 * @internal This implementation should only be used for testing purposes.
 */
final class FakeConfig implements Config
{
    private string $lokaliseXSecret;
    private bool $areIpRestrictionsEnabled = true;

    public static function swap(Application $app): self
    {
        return tap(new self(), function (self $instance) use ($app): void {
            $app->instance(self::class, $instance);
            $app->instance(Config::class, $instance);
        });
    }

    public function lokaliseXSecret(): string
    {
        return $this->lokaliseXSecret ??= Str::random();
    }

    public function areIpRestrictionsEnabled(): bool
    {
        return $this->areIpRestrictionsEnabled;
    }

    public function disableIpRestrictions(): void
    {
        $this->areIpRestrictionsEnabled = false;
    }
}
