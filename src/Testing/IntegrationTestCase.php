<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Testing;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\FakeExceptionHandler;
use Craftzing\Laravel\LokaliseWebhooks\LokaliseWebhooksServiceProvider;
use Craftzing\Laravel\LokaliseWebhooks\Subscribers\CopyExportedProjectToStorage;
use Craftzing\Laravel\LokaliseWebhooks\Testing\Doubles\FakeConfig;
use CreateWebhookCallsTable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class IntegrationTestCase extends OrchestraTestCase
{
    protected bool $shouldFakeEvents = true;
    protected bool $shouldFakeConfig = true;
    protected bool $shouldFakeListeners = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        include_once __DIR__ .
            '/../../vendor/spatie/laravel-webhook-client/database/migrations/create_webhook_calls_table.php.stub';

        (new CreateWebhookCallsTable())->up();
    }

    protected function setUpTraits(): array
    {
        Bus::fake();
        Queue::fake();
        Storage::fake();
        FakeExceptionHandler::swap($this->app);

        if ($this->shouldFakeEvents) {
            Event::fake();
        }

        if ($this->shouldFakeConfig) {
            FakeConfig::swap($this->app);
        }

        if ($this->shouldFakeListeners) {
            $this->swap(
                CopyExportedProjectToStorage::class,
                new CopyExportedProjectToStorage(Storage::disk(), __DIR__ . '/../../stubs/'),
            );
        }

        return parent::setUpTraits();
    }

    /**
     * @return array<string>
     */
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
