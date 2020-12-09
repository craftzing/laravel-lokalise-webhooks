<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http;

use Craftzing\Laravel\LokaliseWebhooks\Config;
use Craftzing\Laravel\LokaliseWebhooks\IntegrationTestCase;
use Craftzing\Laravel\LokaliseWebhooks\ProcessLokaliseWebhook;
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
            [],
        ];

        yield 'It fails when the secret header is empty' => [
            [self::EXPECTED_SECRET_HEADER_NAME => ''],
        ];

        yield 'It fails when the secret header does not match the expected one' => [
            [self::EXPECTED_SECRET_HEADER_NAME => 'some-unexpected-secret'],
        ];
    }

    /**
     * @test
     * @dataProvider invalidIncomingWebhooks
     */
    public function itFailsWhenTheIncomingWebhookIsInvalid(array $headers): void
    {
        $this->expectExceptionObject(WebhookFailed::invalidSignature());

        $response = $this->post(self::URI, [], $headers);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        Bus::assertNotDispatched(ProcessLokaliseWebhook::class);
    }

    /**
     * @test
     */
    public function itCanHandleValidIncomingWebhooks(): void
    {
        $response = $this->post(self::URI, [], [
            self::EXPECTED_SECRET_HEADER_NAME => $this->app[Config::class]->lokaliseXSecret(),
        ]);

        $response->assertOk();
        Bus::assertDispatched(ProcessLokaliseWebhook::class);
    }
}
