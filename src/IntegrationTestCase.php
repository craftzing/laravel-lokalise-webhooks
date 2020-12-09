<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class IntegrationTestCase extends OrchestraTestCase
{
    protected Config $config;
    protected bool $shouldFakeConfig = true;

    protected function setUpTraits(): void
    {
        $uses = parent::setUpTraits();

        $this->fakeConfig();
    }

    private function fakeConfig(): void
    {
        if ($this->shouldFakeConfig) {
            $this->config = FakeConfig::swap($this->app);
        }
    }

    /**
     * @after
     */
    public function unsetConfig(): void
    {
        unset($this->config);
    }

    protected function getPackageProviders($app): array
    {
        return [LokaliseWebhooksServiceProvider::class];
    }
}
