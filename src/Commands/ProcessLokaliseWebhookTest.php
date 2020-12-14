<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Commands;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Craftzing\Laravel\LokaliseWebhooks\Testing\IntegrationTestCase;
use Exception;
use Generator;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookClient\Models\WebhookCall;

use function compact;

final class ProcessLokaliseWebhookTest extends IntegrationTestCase
{
    public function invalidWebhookPayloads(): Generator
    {
        yield 'Missing an event property' => [
            [],
            UnexpectedWebhookPayload::missingEvent(),
        ];

        yield 'Event property is missing' => [
            ['event' => ''],
            UnexpectedWebhookPayload::missingEvent(),
        ];
    }

    /**
     * @test
     * @dataProvider invalidWebhookPayloads
     */
    public function itFailsWhenTheWebhookPayloadIsInvalid(array $payload, Exception $exception): void
    {
        $this->expectExceptionObject($exception);

        $webhookCall = new WebhookCall(compact('payload'));

        $this->handle(new ProcessLokaliseWebhook($webhookCall));
    }

    /**
     * @test
     */
    public function itCanHandleIncomingWebhooks(): void
    {
        $webhookCall = new WebhookCall([
            'payload' => [
                'event' => 'something.happened',
            ],
        ]);

        $this->handle(new ProcessLokaliseWebhook($webhookCall));

        Event::assertDispatched(
            'lokalise-webhooks::something.happened',
            fn (string $event, WebhookCall $expectedWebhookCall) => $webhookCall->is($expectedWebhookCall),
        );
    }

    /**
     * @test
     */
    public function itCanHandleIncomingPingEvents(): void
    {
        $webhookCall = new WebhookCall([
            'payload' => ['ping'],
        ]);

        $this->handle(new ProcessLokaliseWebhook($webhookCall));

        Event::assertDispatched(
            'lokalise-webhooks::ping',
            fn (string $event, WebhookCall $expectedWebhookCall) => $webhookCall->is($expectedWebhookCall),
        );
    }
}
