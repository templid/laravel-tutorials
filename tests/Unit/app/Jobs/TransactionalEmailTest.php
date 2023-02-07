<?php

declare(strict_types=1);

namespace Tests\Unit\App\Jobs;

use Tests\TestCase;
use App\Dto\EnvelopeDto;
use Illuminate\Mail\Mailable;
use App\Jobs\TransactionalEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Address;
use PHPUnit\Framework\MockObject\MockObject;
use App\Builder\TransactionalMailableBuilder;

class TransactionalEmailTest extends TestCase
{
    /**
     * @var MockObject|Address
     */
    protected $to;

    /**
     * @var MockObject|Mailable
     */
    protected $mailable;

    /**
     * @var MockObject|TransactionalMailableBuilder
     */
    protected $mailableBuilder;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->to              = $this->createMock(Address::class);
        $this->mailableBuilder = $this->createMock(TransactionalMailableBuilder::class);
        $this->mailable        = $this->createMock(Mailable::class);

        $this->mailable->method('to')->willReturn($this->mailable);
        $this->mailable->method('cc')->willReturn($this->mailable);
        $this->mailable->method('bcc')->willReturn($this->mailable);
    }

    /**
     * @return void
     */
    public function testHandleSendsEmailWithoutDataAndEnvelope(): void
    {
        Mail::fake();

        $templateId = 1;

        $this->mailableBuilder
            ->expects($this->once())
            ->method('build')
            ->with($templateId, null, null)
            ->willReturn($this->mailable);

        $transactionalEmailJob = new TransactionalEmail($this->to, $templateId);

        $transactionalEmailJob->handle($this->mailableBuilder);

        Mail::assertSent(get_class($this->mailable));
    }

    /**
     * @return void
     */
    public function testHandleSendsEmailWithDataAndEnvelope(): void
    {
        Mail::fake();

        $templateId = 1;
        $data       = collect(['foo' => 'bar']);

        /** @var MockObject|EnvelopeDto $envelope */
        $envelope = $this->createMock(EnvelopeDto::class);

        $this->mailableBuilder
            ->expects($this->once())
            ->method('build')
            ->with($templateId, $data, $envelope)
            ->willReturn($this->mailable);

        $transactionalEmailJob = new TransactionalEmail(
            $this->to,
            $templateId,
            $data,
            $envelope
        );

        $transactionalEmailJob->handle($this->mailableBuilder);

        Mail::assertSent(get_class($this->mailable));
    }
}
