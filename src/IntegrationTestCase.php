<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\FakeExceptionHandler;
use CreateWebhookCallsTable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class IntegrationTestCase extends OrchestraTestCase
{
    protected bool $shouldFakeConfig = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        include_once __DIR__ .
            '/../vendor/spatie/laravel-webhook-client/database/migrations/create_webhook_calls_table.php.stub';

        (new CreateWebhookCallsTable())->up();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpTraits(): void
    {
        $uses = parent::setUpTraits();

        Bus::fake();
        Event::fake();
        FakeExceptionHandler::swap($this->app);

        if ($this->shouldFakeConfig) {
            FakeConfig::swap($this->app);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [LokaliseWebhooksServiceProvider::class];
    }

    /**
     * @param object $class
     * @return mixed
     */
    public function handle(object $class)
    {
        return $this->app->call([$class, 'handle']);
    }
}
