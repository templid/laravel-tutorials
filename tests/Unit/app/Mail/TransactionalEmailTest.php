<?php

declare(strict_types=1);

namespace Tests\Unit\App\Mail;

use Tests\TestCase;
use App\Dto\EnvelopeDto;
use App\Mail\TransactionalEmail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\HtmlString;

class TransactionalEmailTest extends TestCase
{
    /**
     * @return void
     */
    public function testEnvelopeReturnsEmptyEnvelopeWhenEnvelopeDtoIsNull(): void
    {
        $mailable = new TransactionalEmail();

        $envelope = $mailable->envelope();

        $this->assertNull($envelope->from);
        $this->assertNull($envelope->subject);

        $this->assertEmpty($envelope->to);
        $this->assertEmpty($envelope->cc);
        $this->assertEmpty($envelope->bcc);
        $this->assertEmpty($envelope->replyTo);
        $this->assertEmpty($envelope->tags);
        $this->assertEmpty($envelope->metadata);
        $this->assertEmpty($envelope->using);
    }

    /**
     * @return void
     */
    public function testSetEnvelopeSetsEnvelopeWithDataWhenEnvelopeDtoIsNotNull(): void
    {
        $from     = new Address('jhon@doe.com', 'Jhon Doe');
        $to       = ['to@email.com'];
        $cc       = ['cc@email.com'];
        $bcc      = ['bcc@email.com'];
        $replyTo  = ['replyTo@email.com'];
        $tags     = ['tag1', 'tag2'];
        $metadata = ['foo' => 'bar'];
        $using    = function () {};

        $envelopeDto = new EnvelopeDto(
            $from,
            $to,
            $cc,
            $bcc,
            $replyTo,
            $tags,
            $metadata,
            $using
        );

        $mailable = new TransactionalEmail();
        $mailable->setEnvelope($envelopeDto);

        $envelope = $mailable->envelope();

        $this->assertSame($from, $envelope->from);

        foreach ($envelope->to as $key => $address) {
            $this->assertSame($to[$key], $address->address);
        }

        foreach ($envelope->cc as $key => $address) {
            $this->assertSame($cc[$key], $address->address);
        }

        foreach ($envelope->bcc as $key => $address) {
            $this->assertSame($bcc[$key], $address->address);
        }

        foreach ($envelope->replyTo as $key => $address) {
            $this->assertSame($replyTo[$key], $address->address);
        }

        foreach ($envelope->tags as $key => $tag) {
            $this->assertSame($tags[$key], $tag);
        }

        foreach ($envelope->metadata as $key => $value) {
            $this->assertSame($metadata[$key], $value);
        }

        $this->assertSame($using, $envelope->using[0]);
    }

    /**
     * @return void
     */
    public function testSetContentSetsHtmlAndPlainTest(): void
    {
        $html = '<h1>HTML</h1>';
        $plain = 'Plain';

        $mailable = (new TransactionalEmail())
            ->setContent($html, $plain);

        /** @var HtmlString $viewHtml */
        $viewHtml = $mailable->view['html'];

        $this->assertInstanceOf(HtmlString::class, $viewHtml);
        $this->assertSame($html, $viewHtml->toHtml());
        $this->assertSame($plain, $mailable->view['raw']);
    }

    /**
     * @return void
     */
    public function testSetContentSetsOnlyHtmlWhenPlainIsEmpty(): void
    {
        $html = '<h1>HTML</h1>';

        $mailable = (new TransactionalEmail())
            ->setContent($html);

        $this->assertSame($html, $mailable->render());
        $this->assertNull($mailable->view);
    }
}