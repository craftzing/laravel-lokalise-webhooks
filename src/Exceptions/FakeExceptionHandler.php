<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @internal This implementation should only be used for testing purposes.
 */
final class FakeExceptionHandler extends Handler
{
    public static function swap(Application $app): self
    {
        return $app->instance(ExceptionHandler::class, new self($app));
    }

    public function report(Throwable $e): void
    {
        // Nothing should be reported...
    }

    public function render($request, Throwable $e): Response
    {
        throw $e;
    }
}
