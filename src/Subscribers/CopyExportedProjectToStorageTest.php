<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Subscribers;

use Craftzing\Laravel\LokaliseWebhooks\Event;
use Craftzing\Laravel\LokaliseWebhooks\IntegrationTestCase;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event as IlluminateEvent;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\WebhookClient\Models\WebhookCall;

final class CopyExportedProjectToStorageTest extends IntegrationTestCase
{
    protected bool $shouldFakeEvents = false;

    /**
     * @test
     */
    public function itCanBeRegisteredAsASubscriberForTheProjectExportedEvent(): void
    {
        IlluminateEvent::subscribe(CopyExportedProjectToStorage::class);

        IlluminateEvent::dispatch(Event::PROJECT_EXPORTED, new WebhookCall());

        Queue::assertPushed(
            CallQueuedListener::class,
            fn (CallQueuedListener $listener) => $listener->class === CopyExportedProjectToStorage::class,
        );
    }

    /**
     * @test
     */
    public function itCanCopyAnExportedProjectToStorage(): void
    {
        $webhookCall = new WebhookCall([
            'payload' => [
                'export' => [
                    'filename' => 'Project-export.zip',
                ],
            ],
        ]);

        $this->app[CopyExportedProjectToStorage::class]($webhookCall);

        // The /stubs directory should contain a zip archive with 2 translation files. The
        // event subscriber should copy the zip archive to the provided storage disk,
        // extract it's contents and eventually cleanup the archive itself...
        Storage::assertExists(['en.json', 'nl.json']);
        Storage::assertMissing('Project-export.zip');
    }
}
