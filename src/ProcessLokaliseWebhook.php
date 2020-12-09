<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnexpectedWebhookPayload;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\ProcessWebhookJob;

final class ProcessLokaliseWebhook extends ProcessWebhookJob
{
    private string $eventName;

    public function __construct(WebhookCall $webhookCall)
    {
        parent::__construct($webhookCall);

        $this->eventName = $this->eventName($webhookCall);
    }

    private function eventName(): string
    {
        if ($eventName = $this->webhookCall->payload['event'] ?? null) {
            return $eventName;
        }

        throw UnexpectedWebhookPayload::missingEvent();
    }

    public function handle(): void
    {

    }
}
