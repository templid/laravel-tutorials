<?php

declare(strict_types=1);

namespace App\Fetcher;

use App\Dto\TemplidTemplateDto;
use Illuminate\Support\Collection;

class TemplidTemplateFetcher
{
    /**
     * @param int             $templatedId
     * @param Collection|null $data
     *
     * @return TemplidTemplateDto
     */
    public function fetch(int $templatedId, ?Collection $data = null): TemplidTemplateDto
    {
        return new TemplidTemplateDto(
            subject: 'Hello world subject',
            html: '<div><h1>Hello world</h1></div>',
            text: '',
        );
    }
}