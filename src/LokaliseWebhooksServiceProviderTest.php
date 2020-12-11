<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Http\Requests\HandleLokaliseWebhooksRequest;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

final class LokaliseWebhooksServiceProviderTest extends IntegrationTestCase
{
    private const URI = 'lokalise/webhooks/handle';

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
}
