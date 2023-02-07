<?php

declare(strict_types=1);

namespace App\Fetcher;

use App\Dto\TemplidTemplateDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use App\Exceptions\RateLimitException;
use Illuminate\Http\Client\PendingRequest;
use App\Fetcher\Validator\TemplidFetcherResponseValidator;
use Exception;

class TemplidTemplateFetcher
{
    protected const API_URL = 'https://api.templid.com/v1/templates/%s/render';

    /**
     * @param PendingRequest                  $request
     * @param TemplidFetcherResponseValidator $validator
     */
    public function __construct(
        protected PendingRequest                  $request,
        protected TemplidFetcherResponseValidator $validator
    ){}

    /**
     * @param int             $templatedId
     * @param Collection|null $data
     *
     * @return TemplidTemplateDto
     */
    public function fetch(int $templatedId, ?Collection $data = null): TemplidTemplateDto
    {
        /** @var array{subject: string, html: string, text: string} $responseData*/
        $responseData = $this->sendRequest(
            $templatedId,
            $data?->toArray() ?? []
        )->json();

        return new TemplidTemplateDto(
            subject: $responseData['subject'],
            html: $responseData['html'],
            text: $responseData['text'],
        );
    }

    /**
     * @param int          $templatedId
     * @param array<mixed> $data
     * 
     * @return Response
     */
    protected function sendRequest(int $templatedId, array $data = []): Response
    {
        $attempts = 0;
        $retry    = 3;

        $url = sprintf(self::API_URL, $templatedId);

        do {
            try {
                $response = $this->request
                    ->withToken(env('TEMPLID_TOKEN'))
                    ->post($url, $data);

                $this->validator->validate($response);

                return $response;
            } catch (RateLimitException $e) {
                Log::notice($e->getMessage());

                sleep(1);

                $attempts++;
            }
        } while ($attempts < $retry);

        throw new Exception('Unable to fetch template');
    }
}
