<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Closure;
use Craftzing\Laravel\LokaliseWebhooks\Http\Requests\HandleLokaliseWebhooksRequest;
use Craftzing\Laravel\LokaliseWebhooks\Subscribers\CopyExportedProjectToStorage;
use Craftzing\Laravel\LokaliseWebhooks\Testing\IntegrationTestCase;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Storage;

final class LokaliseWebhooksServiceProviderTest extends IntegrationTestCase
{
    private const URI = 'lokalise/webhooks/handle';

    protected bool $shouldFakeListeners = false;

    /**
     * @test
     */
    public function itExtendsTheRouterToEnableRegisteringARouteToHandleIncomingWebhooks(): void
    {
        $router = $this->app[Router::class];

        $router->lokaliseWebhooks(self::URI);

        $this->assertInstanceOf(
            Route::class,
            $route = $router->getRoutes()->getByAction(HandleLokaliseWebhooksRequest::class),
        );
        $this->assertSame(self::URI, $route->uri);
    }

    /**
     * @test
     */
    public function itBindsTheCopyExportedProjectToStorageSubscriberWithTheDefaultStorageDisk(): void
    {
        $assertWasBoundWithDefaultStorageDisk = Closure::bind(function (CopyExportedProjectToStorage $listener) {
            return Storage::disk() === $listener->storage;
        }, null, CopyExportedProjectToStorage::class);

        $listener = $this->app[CopyExportedProjectToStorage::class];

        $this->assertTrue($assertWasBoundWithDefaultStorageDisk($listener));
    }
}
