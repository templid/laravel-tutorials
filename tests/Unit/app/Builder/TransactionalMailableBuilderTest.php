<?php

declare(strict_types=1);

namespace Tests\Unit\App\Builder;

use App\Dto\EnvelopeDto;
use Illuminate\Mail\Mailable;
use App\Dto\TemplidTemplateDto;
use PHPUnit\Framework\TestCase;
use App\Mail\TransactionalEmail;
use App\Fetcher\TemplidTemplateFetcher;
use PHPUnit\Framework\MockObject\MockObject;
use App\Builder\TransactionalMailableBuilder;

class TransactionalMailableBuilderTest extends TestCase
{
    /**
     * @var MockObject|TemplidTemplateFetcher
     */
    protected MockObject $fetcher;

    /**
     * @var MockObject|TransactionalEmail
     */
    protected MockObject $transactionalEmail;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->fetcher            = $this->createMock(TemplidTemplateFetcher::class);
        $this->transactionalEmail = $this->createMock(TransactionalEmail::class);
    }

    /**
     * @return void
     */
    public function testBuildWithoutDataAndWithoutEnvelopeReturnsMailable(): void
    {
        $subject = 'Foo subject';
        $html    = 'Foo html';
        $text    = 'Foo text';

        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->with(1, null)
            ->willReturn(new TemplidTemplateDto(
                $subject,
                $html,
                $text
            ));

        $this->transactionalEmail
            ->expects($this->once())
            ->method('subject')
            ->with($subject)
            ->willReturn($this->transactionalEmail);

        $this->transactionalEmail
            ->expects($this->once())
            ->method('setContent')
            ->with($html, $text)
            ->willReturn($this->transactionalEmail);

        $this->transactionalEmail
            ->expects($this->once())
            ->method('setEnvelope')
            ->with(null)
            ->willReturn($this->transactionalEmail);

        $builder = $this->getBuilder();

        $this->assertInstanceOf(Mailable::class, $builder->build(1));
    }

    /**
     * @return void
     */
    public function testBuildWithDataAndWithEnvelopeReturnsMailable(): void
    {
        $subject = 'Foo subject';
        $html    = 'Foo html';
        $text    = 'Foo text';

        $data = collect([
            'foo' => 'bar',
        ]);

        $envelope = new EnvelopeDto();

        $this->fetcher
            ->expects($this->once())
            ->method('fetch')
            ->with(1, $data)
            ->willReturn(new TemplidTemplateDto(
                $subject,
                $html,
                $text
            ));
        
        $this->transactionalEmail
            ->expects($this->once())
            ->method('subject')
            ->with($subject)
            ->willReturn($this->transactionalEmail);

        $this->transactionalEmail
            ->expects($this->once())
            ->method('setContent')
            ->with($html, $text)
            ->willReturn($this->transactionalEmail);

        $this->transactionalEmail
            ->expects($this->once())
            ->method('setEnvelope')
            ->with($envelope)
            ->willReturn($this->transactionalEmail);

        $builder = $this->getBuilder();

        $this->assertInstanceOf(Mailable::class, $builder->build(1, $data, $envelope));
    }

    /**
     * @return TransactionalMailableBuilder
     */
    protected function getBuilder(): TransactionalMailableBuilder
    {
        return new TransactionalMailableBuilder(
            $this->fetcher,
            $this->transactionalEmail,
        );
    }
}
