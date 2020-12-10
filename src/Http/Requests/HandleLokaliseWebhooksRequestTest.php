<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Craftzing\Laravel\LokaliseWebhooks\Commands\ProcessLokaliseWebhook;
use Craftzing\Laravel\LokaliseWebhooks\Config;
use Craftzing\Laravel\LokaliseWebhooks\IntegrationTestCase;
use Exception;
use Generator;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Bus;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Symfony\Component\HttpFoundation\Response;

final class HandleLokaliseWebhooksRequestTest extends IntegrationTestCase
{
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

    public function invalidIncomingWebhooks(): Generator
    {
        yield 'Secret header is missing' => [
            fn () => [],
            WebhookFailed::invalidSignature(),
        ];

        yield 'Secret header is empty' => [
            fn () => [self::EXPECTED_SECRET_HEADER_NAME => ''],
            WebhookFailed::invalidSignature(),
        ];

        yield 'Secret header does not match the expected one' => [
            fn () => [self::EXPECTED_SECRET_HEADER_NAME => 'some-unexpected-secret'],
            WebhookFailed::invalidSignature(),
        ];
    }

    /**
     * @test
     * @dataProvider invalidIncomingWebhooks
     */
    public function itFailsWhenTheIncomingWebhookIsInvalid(callable $headers, Exception $exception): void
    {
        $this->expectExceptionObject($exception);
        $headers = $headers($this->app[Config::class]->lokaliseXSecret());

        $response = $this->post(self::URI, [], $headers);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Bus::assertNotDispatched(ProcessLokaliseWebhook::class);
    }

    /**
     * @test
     */
    public function itCanHandleIncomingWebhooks(): void
    {
        $response = $this->post(self::URI, [], [
            self::EXPECTED_SECRET_HEADER_NAME => $this->app[Config::class]->lokaliseXSecret(),
        ]);

        $response->assertOk();
        Bus::assertDispatched(ProcessLokaliseWebhook::class);
    }
}
