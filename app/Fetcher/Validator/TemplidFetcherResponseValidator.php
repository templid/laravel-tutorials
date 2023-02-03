<?php

declare(strict_types=1);

namespace App\Fetcher\Validator;

use Exception;
use Illuminate\Http\Client\Response;
use App\Exceptions\RateLimitException;

class TemplidFetcherResponseValidator
{
    /**
     * @param Response $response
     *
     * @return void
     */
    public function validate(Response $response): void
    {
        if ($response->status() === 401) {
            throw new Exception('Unauthorized');
        }

        if ($response->status() === 404) {
            throw new Exception('Template not found');
        }

        if ($response->status() === 429) {
            throw new RateLimitException('Too many requests');
        }

        if ($response->status() !== 200) {
            throw new Exception('API error');
        }
    }
}