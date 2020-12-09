<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Craftzing\Laravel\LokaliseWebhooks\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class HandleLokaliseWebhooksRequest
{
    public function __invoke(Request $request, Config $config): JsonResponse
    {
    }
}
