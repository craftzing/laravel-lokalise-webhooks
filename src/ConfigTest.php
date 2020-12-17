<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\AppMisconfigured;
use Craftzing\Laravel\LokaliseWebhooks\Testing\IntegrationTestCase;
use Exception;
use Generator;
use Illuminate\Support\Str;

use function config;

final class ConfigTest extends IntegrationTestCase
{
    protected bool $shouldFakeConfig = false;

    /**
     * @before
     */
    public function setupConfig(): void
    {
        $this->afterApplicationCreated(function (): void {
            config(['lokalise-webhooks.x_secret' => Str::random()]);
        });
    }

    public function misconfiguredApp(): Generator
    {
        yield 'Missing Lokalise X-Secret' => [
            ['lokalise-webhooks.x_secret' => null],
            AppMisconfigured::missingLokaliseXSecret(),
        ];

        yield 'Empty Lokalise X-Secret' => [
            ['lokalise-webhooks.x_secret' => ''],
            AppMisconfigured::missingLokaliseXSecret(),
        ];
    }

    /**
     * @test
     * @dataProvider misconfiguredApp
     */
    public function itFailsToResolveWhenTheAppIsMisconfigured(array $config, Exception $exception): void
    {
        config($config);

        $this->expectExceptionObject($exception);

        $this->app[Config::class];
    }

    /**
     * @test
     */
    public function itCanBeResolvedFromTheContainer(): void
    {
        $config = $this->app[Config::class];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame(config('lokalise-webhooks.x_secret'), $config->lokaliseXSecret());
    }

    /**
     * @test
     */
    public function itEnablesIpRestrictionsByDefault(): void
    {
        $config = $this->app[Config::class];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertTrue($config->areIpRestrictionsEnabled());
    }

    /**
     * @test
     */
    public function itCanDisableIpRestrictions(): void
    {
        config(['lokalise-webhooks.enable_ip_restrictions' => false]);

        $config = $this->app[Config::class];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertFalse($config->areIpRestrictionsEnabled());
    }
}
