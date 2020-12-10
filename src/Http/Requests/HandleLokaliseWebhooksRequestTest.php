<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Craftzing\Laravel\LokaliseWebhooks\Config;
use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Craftzing\Laravel\LokaliseWebhooks\IntegrationTestCase;
use Craftzing\Laravel\LokaliseWebhooks\ProcessLokaliseWebhook;
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
        $this->afterApplicationCreated(function () {
            $this->app[Router::class]->lokaliseWebhooks(self::URI);
        });
    }

    public function invalidIncomingWebhooks(): Generator
    {
        yield 'It fails when the secret header is missing' => [
            WebhookFailed::invalidSignature(),
            fn () => [],
        ];

        yield 'It fails when the secret header is empty' => [
            WebhookFailed::invalidSignature(),
            fn () => [self::EXPECTED_SECRET_HEADER_NAME => ''],
        ];

        yield 'It fails when the secret header does not match the expected one' => [
            WebhookFailed::invalidSignature(),
            fn () => [self::EXPECTED_SECRET_HEADER_NAME => 'some-unexpected-secret'],
        ];

        yield 'The webhook payload is missing an event property' => [
            UnexpectedWebhookPayload::missingEvent(),
            fn (string $secret) => [self::EXPECTED_SECRET_HEADER_NAME => $secret],
        ];

        yield 'The webhook payload is has an empty event property' => [
            UnexpectedWebhookPayload::missingEvent(),
            fn (string $secret) => [self::EXPECTED_SECRET_HEADER_NAME => $secret],
            ['event' => ''],
        ];
    }

    /**
     * @test
     * @dataProvider invalidIncomingWebhooks
     */
    public function itFailsWhenTheIncomingWebhookIsInvalid(
        Exception $exception,
        callable $headers,
        array $payload = []
    ): void {
        $this->expectExceptionObject($exception);
        $headers = $headers($this->app[Config::class]->lokaliseXSecret());

        $response = $this->post(self::URI, $payload, $headers);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Bus::assertNotDispatched(ProcessLokaliseWebhook::class);
    }

    /**
     * @test
     */
    public function itCanHandleIncomingWebhooks(): void
    {
        $response = $this->post(self::URI,
            ['event' => 'something.happened'],
            [self::EXPECTED_SECRET_HEADER_NAME => $this->app[Config::class]->lokaliseXSecret()],
        );

        $response->assertOk();
        Bus::assertDispatched(ProcessLokaliseWebhook::class);
    }
}
