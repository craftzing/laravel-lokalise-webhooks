<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Exception;
use Generator;
use PHPUnit\Framework\TestCase;
use Spatie\WebhookClient\Models\WebhookCall;

use function compact;

final class EventTest extends TestCase
{
    private string $eventName;
    private array $payload;
    private WebhookCall $webhookCall;

    /**
     * @before
     */
    public function setupWebhookCall(): void
    {
        $this->eventName = 'something.happened';
        $this->payload = ['event' => $this->eventName];
        $this->webhookCall = new WebhookCall(['payload' => $this->payload]);
    }

    /**
     * @after
     */
    public function unsetWebhookCall(): void
    {
        unset($this->eventName, $this->payload, $this->webhookCall);
    }

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
    public function itCannotBeConstructedFromAWebhookCallWithAnInvalidPayload(
        array $payload,
        Exception $exception
    ): void {
        $this->expectExceptionObject($exception);

        $event = Event::fromWebhookCall(new WebhookCall(compact('payload')));

        $this->assertInstanceOf(Event::class, $event);
    }

    /**
     * @test
     */
    public function itCanBeConstructedFromAWebhookCall(): void
    {
        $event = Event::fromWebhookCall($this->webhookCall);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertSame("lokalise-webhooks::{$this->eventName}", $event->name());
    }

    /**
     * @test
     */
    public function itCanBeCastedToAString(): void
    {
        $event = Event::fromWebhookCall($this->webhookCall);

        $this->assertSame($event->name(), (string) $event);
    }
}