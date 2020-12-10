<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Subscribers;

use Craftzing\Laravel\LokaliseWebhooks\Event;
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
    private const AWS_BUCKET_URI = 'http://s3-eu-west-1.amazonaws.com/lokalise-assets/';

    private Filesystem $storage;
    private string $remoteUri;

    public function __construct(Filesystem $storage, string $remoteUri = self::AWS_BUCKET_URI)
    {
        $this->storage = $storage;
        $this->remoteUri = $remoteUri;
    }

    public function __invoke(WebhookCall $webhookCall): void
    {
        $archiveFileName = $this->copyExportFromAwsBucket($webhookCall->payload['export']['filename']);

        $this->extractArchive($archiveFileName);
        $this->cleanupArchive($archiveFileName);
    }

    private function copyExportFromAwsBucket(string $exportFileName): string
    {
        $pathToExport = $this->remoteUri . $exportFileName;
        $archiveFileName = pathinfo($pathToExport)['basename'];
        $stream = fopen($pathToExport, 'r');

        if ($this->storage->put($archiveFileName, $stream)) {
            fclose($stream);
        } else {
            throw UnableToCopyExportFileToStorage::streamError();
        }

        return $archiveFileName;
    }

    private function extractArchive(string $archiveFileName): void
    {
        $zip = new ZipArchive();

        if ($zip->open($this->storage->path($archiveFileName))) {
            $zip->extractTo($this->storage->path(''));
            $zip->close();
        } else {
            throw UnableToCopyExportFileToStorage::extractingArchiveFailed($this->storage->path($archiveFileName));
        }
    }

    private function cleanupArchive(string $archiveFileName): void
    {
        $this->storage->delete($archiveFileName);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Event::PROJECT_EXPORTED, self::class);
    }
}
