<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use RuntimeException;

final class UnableToCopyExportFileToStorage extends RuntimeException
{
    public static function streamError(): self
    {
        return new self('Something went wrong while streaming the export to storage disk.');
    }

    public static function archiveCouldNotBeOpened(string $pathToArchive, int $errorCode): self
    {
        return new self(
            "Opening the archive `$pathToArchive` failed with error code `$errorCode`. " .
            'Check https://www.php.net/manual/en/ziparchive.open.php for more info.'
        );
    }
}
