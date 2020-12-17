<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Exceptions;

use Exception;

final class RequestRestrictedToLokaliseIPs extends Exception
{
    public static function ipIsNotALokaliseIP(string $ip): self
    {
        return new self(
            "Requests to this route are restricted to known Lokalise IP's. `$ip` is not a Lokalise IP. " .
            'If your application sits behind a load balancer or any intermediary (reverse) proxy, ' .
            'make sure to list it as a trusted proxy so we can access the real end-client IP.'
        );
    }
}
