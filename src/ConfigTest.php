<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\AppMisconfigured;
use Exception;
use Generator;
use Illuminate\Support\Str;

use function config;

final class ConfigTest extends IntegrationTestCase
{
    protected bool $shouldFakeConfig = false;

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
        config(['lokalize-webhooks.x_secret' => $xSecret = Str::random()]);

        $config = $this->app[Config::class];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame($xSecret, $config->lokaliseXSecret());
    }
}
