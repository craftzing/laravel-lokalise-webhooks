<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http\Requests;

use Craftzing\Laravel\LokaliseWebhooks\Commands\ProcessLokaliseWebhook;
use Craftzing\Laravel\LokaliseWebhooks\Config;
use Craftzing\Laravel\LokaliseWebhooks\Exceptions\RequestRestrictedToLokaliseIPs;
use Craftzing\Laravel\LokaliseWebhooks\Testing\Doubles\FakeConfig;
use Craftzing\Laravel\LokaliseWebhooks\Testing\IntegrationTestCase;
use Exception;
use Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Spatie\WebhookClient\Exceptions\WebhookFailed;

use function iterator_to_array;

final class HandleLokaliseWebhooksRequestTest extends IntegrationTestCase
{
    use WithFaker;

    private const URI = '/lokalize/webhooks/handle';
    private const EXPECTED_SECRET_HEADER_NAME = 'X-Secret';

    /**
     * @before
     */
    public function registerWebhooksHandlerRoute(): void
    {
        $this->afterApplicationCreated(function (): void {
            $this->app[Router::class]->lokaliseWebhooks(self::URI);
        });
    }

    /**
     * @test
     */
    public function itFailsWhenTheIncomingWebhookDoesNotComeFromALokaliseIP(): void
    {
        $ip = $this->faker->ipv4;

        $this->expectExceptionObject(RequestRestrictedToLokaliseIPs::ipIsNotALokaliseIP($ip));

        $this->post(self::URI, [], ['REMOTE_ADDR' => $ip]);
    }

    public function invalidIncomingWebhooks(): Generator
    {
        yield 'Secret header is missing' => [
            [],
            WebhookFailed::invalidSignature(),
        ];

        yield 'Secret header is empty' => [
            [self::EXPECTED_SECRET_HEADER_NAME => ''],
            WebhookFailed::invalidSignature(),
        ];

        yield 'Secret header does not match the expected one' => [
            [self::EXPECTED_SECRET_HEADER_NAME => 'some-unexpected-secret'],
            WebhookFailed::invalidSignature(),
        ];
    }

    /**
     * @test
     * @dataProvider invalidIncomingWebhooks
     */
    public function itFailsWhenTheIncomingWebhookIsInvalid(array $headers, Exception $exception): void
    {
        $this->expectExceptionObject($exception);

        $lokaliseIps = Arr::flatten(iterator_to_array($this->lokaliseIps()));

        $this->post(self::URI, [], $headers + [
            'REMOTE_ADDR' => Arr::random($lokaliseIps),
        ]);
    }

    public function lokaliseIps(): Generator
    {
        yield ['159.69.72.82'];
        yield ['94.130.129.39'];
        yield ['195.201.158.210'];
        yield ['94.130.129.237'];
    }

    /**
     * @test
     * @dataProvider lokaliseIps
     */
    public function itCanHandleIncomingWebhooksFromALokaliseIP(string $ip): void
    {
        $response = $this->post(self::URI, [], [
            'REMOTE_ADDR' => $ip,
            self::EXPECTED_SECRET_HEADER_NAME => $this->app[Config::class]->lokaliseXSecret(),
        ]);

        $response->assertOk();
        Bus::assertDispatched(ProcessLokaliseWebhook::class);
    }

    /**
     * @test
     */
    public function itCanExplicitlyHandleIncomingWebhooksFromIPsThatDoesNotBelongToLokalise(): void
    {
        $this->app[FakeConfig::class]->disableIpRestrictions();

        $response = $this->post(self::URI, [], [
            'REMOTE_ADDR' => $this->faker->ipv4,
            self::EXPECTED_SECRET_HEADER_NAME => $this->app[Config::class]->lokaliseXSecret(),
        ]);

        $response->assertOk();
        Bus::assertDispatched(ProcessLokaliseWebhook::class);
    }
}
