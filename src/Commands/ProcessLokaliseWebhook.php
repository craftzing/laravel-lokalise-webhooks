<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Commands;

use Craftzing\Laravel\LokaliseWebhooks\LokaliseEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Spatie\WebhookClient\ProcessWebhookJob;

final class ProcessLokaliseWebhook extends ProcessWebhookJob
{
    public function handle(Dispatcher $events): void
    {
        $events->dispatch(
            (string) LokaliseEvent::fromWebhookCall($this->webhookCall),
            $this->webhookCall,
        );
    }
}
