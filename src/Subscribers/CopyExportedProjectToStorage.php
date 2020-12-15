<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Subscribers;

use Craftzing\Laravel\LokaliseWebhooks\LokaliseEvent;
use Craftzing\Laravel\LokaliseWebhooks\Exceptions\UnableToCopyExportFileToStorage;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;
use ZipArchive;

use function fclose;
use function fopen;
use function pathinfo;

final class CopyExportedProjectToStorage implements ShouldQueue
{
    private const REMOTE_AWS_BUCKET = 'http://s3-eu-west-1.amazonaws.com/lokalise-assets/';

    private Filesystem $storage;
    private string $remote;

    public function __construct(Filesystem $storage, string $remote = self::REMOTE_AWS_BUCKET)
    {
        $this->storage = $storage;
        $this->remote = $remote;
    }

    public function __invoke(WebhookCall $webhookCall): void
    {
        $payload = $webhookCall->getAttribute('payload');
        $archiveFileName = $this->copyExportFromAwsBucket($payload['export']['filename']);

        $this->extractArchive($archiveFileName);
        $this->cleanupArchive($archiveFileName);
    }

    private function copyExportFromAwsBucket(string $exportFileName): string
    {
        $pathToExport = $this->remote . $exportFileName;
        $archiveFileName = pathinfo($pathToExport)['basename'];
        $stream = fopen($pathToExport, 'r');

        if (! $this->storage->put($archiveFileName, $stream)) {
            throw UnableToCopyExportFileToStorage::streamError();
        }

        fclose($stream);

        return $archiveFileName;
    }

    private function extractArchive(string $archiveFileName): void
    {
        $zip = new ZipArchive();
        $openResponse = $zip->open($this->storage->path($archiveFileName));

        if ($openResponse !== true) {
            throw UnableToCopyExportFileToStorage::archiveCouldNotBeOpened(
                $this->storage->path($archiveFileName),
                $openResponse,
            );
        }

        $zip->extractTo($this->storage->path(''));
        $zip->close();
    }

    private function cleanupArchive(string $archiveFileName): void
    {
        $this->storage->delete($archiveFileName);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(LokaliseEvent::PROJECT_EXPORTED, self::class);
    }
}
