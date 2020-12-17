<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Subscribers;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnableToCopyExportFileToStorage;
use Craftzing\Laravel\LokaliseWebhooks\LokaliseEvent;
use Craftzing\Laravel\LokaliseWebhooks\Testing\Doubles\FakeFilesystem;
use Craftzing\Laravel\LokaliseWebhooks\Testing\IntegrationTestCase;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event as IlluminateEvent;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\WebhookClient\Models\WebhookCall;
use ZipArchive;

use function tap;

final class CopyExportedProjectToStorageTest extends IntegrationTestCase
{
    private const FAKE_REMOTE_PATH = __DIR__ . '/../../stubs/';

    protected bool $shouldFakeEvents = false;

    /**
     * @test
     */
    public function itCanBeRegisteredAsAQueuedSubscriberForTheProjectExportedEvent(): void
    {
        IlluminateEvent::subscribe(CopyExportedProjectToStorage::class);

        IlluminateEvent::dispatch(LokaliseEvent::PROJECT_EXPORTED, new WebhookCall());

        Queue::assertPushed(CallQueuedListener::class, function (CallQueuedListener $listener) {
            return $listener->class === CopyExportedProjectToStorage::class;
        });
    }

    /**
     * @test
     */
    public function itCanBeResolvedFromTheContainer(): void
    {
        $listener = $this->app[CopyExportedProjectToStorage::class];

        $this->assertInstanceOf(CopyExportedProjectToStorage::class, $listener);
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

    /**
     * @test
     */
    public function itFailsWhenTheExportedProjectCouldNotBeCopiedToStorage(): void
    {
        $this->expectExceptionObject(UnableToCopyExportFileToStorage::streamError());

        $filesystem = tap(new FakeFilesystem(), fn (FakeFilesystem $filesystem) => $filesystem->failToWrite());
        $webhookCall = new WebhookCall([
            'payload' => [
                'export' => [
                    'filename' => 'Project-export.zip',
                ],
            ],
        ]);

        (new CopyExportedProjectToStorage($filesystem, self::FAKE_REMOTE_PATH))($webhookCall);
    }

    /**
     * @test
     */
    public function itFailsWhenTheExportedProjectArchiveCouldNotBeOpened(): void
    {
        $filesystem = new FakeFilesystem();

        $this->expectExceptionObject(
            UnableToCopyExportFileToStorage::archiveCouldNotBeOpened(
                $filesystem->root . 'Project-export',
                ZipArchive::CM_DEFLATE64,
            ),
        );

        $webhookCall = new WebhookCall([
            'payload' => [
                'export' => [
                    'filename' => 'Project-export',
                ],
            ],
        ]);

        (new CopyExportedProjectToStorage($filesystem, self::FAKE_REMOTE_PATH))($webhookCall);
    }
}
