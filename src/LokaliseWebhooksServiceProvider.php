<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Http\Requests\HandleLokaliseWebhooksRequest;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

final class LokaliseWebhooksServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__ . '/../config/lokalise-webhooks.php';

    public function boot(Router $router): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => $this->app->configPath('lokalise-webhooks.php'),
            ], 'lokalise-webhooks');
        }

        $router::macro('lokaliseWebhooks', function (string $uri) use ($router): void {
            $router->post($uri, HandleLokaliseWebhooksRequest::class);
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'lokalise-webhooks');

        $this->app->bind(Config::class, IlluminateConfig::class);
    }
}
