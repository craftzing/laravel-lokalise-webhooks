<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\AppMisconfigured;
use Craftzing\Laravel\LokaliseWebhooks\Http\Requests\HandleLokaliseWebhooksRequest;
use Exception;
use Generator;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

use function config;

final class LokaliseWebhooksServiceProviderTest extends IntegrationTestCase
{
    protected bool $shouldFakeConfig = false;

    /**
     * @test
     */
    public function itExtendsTheRouterToEnableRegisteringARouteToHandleIncomingWebhooks(): void
    {
        $router = $this->app[Router::class];
        $uri = 'your-uri';

        $router->lokaliseWebhooks($uri);

        $this->assertInstanceOf(
            Route::class,
            $route = $router->getRoutes()->getByAction(HandleLokaliseWebhooksRequest::class),
        );
        $this->assertSame($uri, $route->uri);
    }

    public function misconfiguredApp(): Generator
    {
        yield 'Missing Lokalise X-Secret' => [
            ['lokalize-webhooks.x_secret' => null],
            AppMisconfigured::missingLokaliseXSecret(),
        ];

        yield 'Empty Lokalise X-Secret' => [
            ['lokalize-webhooks.x_secret' => ''],
            AppMisconfigured::missingLokaliseXSecret(),
        ];
    }

    /**
     * @test
     * @dataProvider misconfiguredApp
     */
    public function itCannotBindTheConfigWhenTheAppIsMisconfigured(array $config, Exception $exception): void
    {
        config($config);

        $this->expectExceptionObject($exception);

        $this->app[Config::class];
    }

    /**
     * @test
     */
    public function itBindsAConfigInstance(): void
    {
        config(['lokalize-webhooks.x_secret' => $xSecret = Str::random()]);

        $config = $this->app[Config::class];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame($xSecret, $config->lokaliseXSecret());
    }
}
