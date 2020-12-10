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

    public static function extractingArchiveFailed(string $pathToArchive): self
    {
        return new self("Archive `$pathToArchive` could not be extracted.");
    }
}
