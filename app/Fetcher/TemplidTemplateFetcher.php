<?php

declare(strict_types=1);

namespace App\Fetcher;

use App\Dto\TemplidTemplateDto;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\PendingRequest;

class TemplidTemplateFetcher
{
    protected const API_URL = 'https://api.templid.com/v1/templates/%s/render';

    /**
     * @param PendingRequest $request
     */
    public function __construct(protected PendingRequest $request)
    {}

    /**
     * @param int             $templatedId
     * @param Collection|null $data
     *
     * @return TemplidTemplateDto
     */
    public function fetch(int $templatedId, ?Collection $data = null): TemplidTemplateDto
    {
        $response = $this->request
            ->withToken(env('TEMPLID_TOKEN'))
            ->post(
                sprintf(self::API_URL, $templatedId),
                $data?->toArray() ?? []
            );

        $responseData = $response->json();

        return new TemplidTemplateDto(
            subject: $responseData['subject'],
            html: $responseData['html'],
            text: $responseData['text'],
        );
    }
}
