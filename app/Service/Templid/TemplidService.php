<?php

declare(strict_types=1);

namespace App\Service\Templid;

use App\Dto\EnvelopeDto;
use Illuminate\Mail\Mailable;
use App\Dto\TemplidTemplateDto;
use App\Mail\TransactionalEmail;
use Illuminate\Support\Collection;
use App\Fetcher\TemplidTemplateFetcher;

class TemplidService
{
    /**
     * @var int 
     */
    protected int $templatedId = 0;

    /**
     * @var Collection|null 
     */
    protected Collection|null $data = null;

    /**
     * @var EnvelopeDto|null 
     */
    protected EnvelopeDto|null $envelopeDto = null;

    /**
     * @param TemplidTemplateFetcher $fetcher
     */
    public function __construct(
        protected TemplidTemplateFetcher $fetcher
    ) {}

    /**
     * @param int $id
     *
     * @return self
     */
    public function setTemplateId(int $id): self
    {
        $this->templatedId = $id;

        return $this;
    }

    /**
     * @param Collection $data
     *
     * @return self
     */
    public function setData(Collection $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param EnvelopeDto $envelopeDto
     *
     * @return self
     */
    public function setEnvelopeDto(EnvelopeDto $envelopeDto): self
    {
        $this->envelopeDto = $envelopeDto;

        return $this;
    }

    /**
     * @return Mailable
     */
    public function buildMailable(): Mailable
    {
        /** @var TemplidTemplateDto $template */
        $template = $this->fetcher->fetch($this->templatedId, $this->data);

        return new TransactionalEmail(
            subjectString: $template->getSubject(),
            htmlContent:   $template->getHtml(),
            plainContent:  $template->getText(),
            envelopeDto:   $this->envelopeDto
        );
    }
}