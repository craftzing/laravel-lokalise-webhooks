<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

final class FakeConfig implements Config
{
    private string $lokaliseXSecret;

    public static function swap(Application $app): self
    {
        return $app->instance(Config::class, new self());
    }

    public function lokaliseXSecret(): string
    {
        return $this->lokaliseXSecret ??= Str::random();
    }
}
