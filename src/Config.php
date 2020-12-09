<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

interface Config
{
    public function lokaliseXSecret(): string;
}
