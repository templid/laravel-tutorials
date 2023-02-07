<?php

namespace App\Mail;

use App\Dto\EnvelopeDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\HtmlString;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class TransactionalEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** 
     * @var EnvelopeDto|null
     */
    protected ?EnvelopeDto $envelopeDto = null;

    /**
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        if ($this->envelopeDto === null) {
            return new Envelope();
        }

        return new Envelope(
            from:     $this->envelopeDto->getFrom(),
            to:       $this->envelopeDto->getTo(),
            cc:       $this->envelopeDto->getCc(),
            bcc:      $this->envelopeDto->getBcc(),
            replyTo:  $this->envelopeDto->getReplyTo(),
            tags:     $this->envelopeDto->getTags(),
            metadata: $this->envelopeDto->getMetadata(),
            using:    $this->envelopeDto->getUsing()
        );
    }

    /**
     * @param string $html
     * @param string $plain
     * 
     * @return self
     */
    public function setContent(string $html = '', string $plain = ''): self
    {
        if ($plain === '') {
            $this->html($html);

            return $this;
        }

        $this->view = [
            'html' => new HtmlString($html),
            'raw'  => $plain,
        ];

        return $this;
    }

    /**
     * @param EnvelopeDto|null $envelopeDto
     * 
     * @return self
     */
    public function setEnvelope(?EnvelopeDto $envelopeDto = null): self
    {
        $this->envelopeDto = $envelopeDto;

        return $this;
    }
}
