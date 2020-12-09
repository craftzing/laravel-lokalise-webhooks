<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LokaliseWebhooksServiceProvider::class];
    }
}
