<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\HtmlString;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {}

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope();
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->subject('Order Shipped subject line');

        $this->setContent(
            html: '<div>HTML content</div>',
            plain: 'Plain text content'
        );
    }

    /**
     * @param string      $html
     * @param string|null $plain
     * 
     * @return void
     */
    protected function setContent(string $html, string $plain = null): void
    {
        if ($plain === null) {
            $this->html($html);

            return;
        }

        $this->view = [
            'html' => new HtmlString($html),
            'raw' => $plain,
        ];
    }
}
