<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Commands;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Craftzing\Laravel\LokaliseWebhooks\IntegrationTestCase;
use Exception;
use Generator;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookClient\Models\WebhookCall;

use function compact;

final class ProcessLokaliseWebhookTest extends IntegrationTestCase
{
    public function invalidPayloads(): Generator
    {
        yield 'The webhook payload is missing an event property' => [
            [],
            UnexpectedWebhookPayload::missingEvent(),
        ];

        yield 'The webhook payload is has an empty event property' => [
            ['event' => ''],
            UnexpectedWebhookPayload::missingEvent(),
        ];
    }

    /**
     * @test
     * @dataProvider invalidPayloads
     */
    public function itFailsWhenThePayloadIsInvalid(array $payload, Exception $exception): void
    {
        $this->expectExceptionObject($exception);

        $webhookCall = new WebhookCall(compact('payload'));

        $this->handle(new ProcessLokaliseWebhook($webhookCall));
    }

    /**
     * @test
     */
    public function itEmitsAnEvent(): void
    {
        $payload = ['event' => 'something.happened'];
        $webhookCall = new WebhookCall(compact('payload'));

        $this->handle(new ProcessLokaliseWebhook($webhookCall));

        Event::assertDispatched(
            'lokalise-webhooks::something.happened',
            function (string $event, WebhookCall $expectedWebhookCall) use ($webhookCall): bool {
                $this->assertTrue($webhookCall->is($expectedWebhookCall));

                return true;
            },
        );
    }
}
