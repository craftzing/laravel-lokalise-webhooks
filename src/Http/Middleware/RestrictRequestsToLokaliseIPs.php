<?php

declare(strict_types=1);

namespace Craftzing\Laravel\LokaliseWebhooks\Http\Middleware;

use Craftzing\Laravel\LokaliseWebhooks\Exceptions\RequestRestrictedToLokaliseIPs;
use Illuminate\Http\Request;

use function in_array;

final class RestrictRequestsToLokaliseIPs
{
    private const LOKALISE_IPS = [
        '159.69.72.82',
        '94.130.129.39',
        '195.201.158.210',
        '94.130.129.237',
    ];

    /**
     * @return mixed
     */
    public function handle(Request $request, callable $next)
    {
        $ip = $request->ip() ?: '';

        if ($this->isLokaliseIP($ip)) {
            throw RequestRestrictedToLokaliseIPs::ipIsNotALokaliseIP($ip);
        }

        return $next($request);
    }

    private function isLokaliseIP(string $ip): bool
    {
        return ! in_array($ip, self::LOKALISE_IPS);
    }
}
