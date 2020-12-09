<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Throwable;

/**
 * @internal This implementation should only be used for testing purposes.
 */
final class FakeExceptionHandler extends Handler
{
    public function __construct()
    {
    }

    public static function swap(Application $app): self
    {
        return $app->instance(ExceptionHandler::class, new self());
    }

    public function report(Throwable $e): void
    {
    }

    public function render($request, Throwable $e): void
    {
        throw $e;
    }
}
