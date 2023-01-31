<?php

declare(strict_types=1);

namespace App\Service\Templid;

use App\Dto\EnvelopeDto;
use Illuminate\Mail\Mailable;
use App\Mail\TransactionalEmail;
use Illuminate\Support\Collection;
use App\Fetcher\TemplidTemplateFetcher;

class TemplidService
{
    /**
     * @param TemplidTemplateFetcher $fetcher
     */
    public function __construct(
        protected TemplidTemplateFetcher $fetcher
    ) {}

    /**
     * @param int              $templateId
     * @param Collection|null  $data
     * @param EnvelopeDto|null $envelopeDto
     * 
     * @return Mailable
     */
    public function buildMailable(
        int $templateId,
        ?Collection $data = null,
        ?EnvelopeDto $envelopeDto = null
    ): Mailable {
        $template = $this->fetcher->fetch($templateId, $data);

        return new TransactionalEmail(
            subjectString: $template->getSubject(),
            htmlContent:   $template->getHtml(),
            plainContent:  $template->getText(),
            envelopeDto:   $envelopeDto
        );
    }
}