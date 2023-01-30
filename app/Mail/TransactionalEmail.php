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
     * @param string           $subjectString
     * @param string           $htmlContent
     * @param string|null      $plainContent
     * @param EnvelopeDto|null $envelopeDto
     */
    public function __construct(
        protected string           $subjectString = '',
        protected string           $htmlContent   = '',
        protected string|null      $plainContent  = null,
        protected EnvelopeDto|null $envelopeDto   = null
    ) {}

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
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
     * @return void
     */
    public function build(): void
    {
        $this->subject($this->subjectString);
        $this->setContent();
    }

    /**
     * @param string      $html
     * @param string|null $plain
     * 
     * @return void
     */
    protected function setContent(): void
    {
        if ($this->plainContent === null) {
            $this->html($this->htmlContent);

            return;
        }

        $this->view = [
            'html' => new HtmlString($this->htmlContent),
            'raw'  => $this->plainContent,
        ];
    }
}
