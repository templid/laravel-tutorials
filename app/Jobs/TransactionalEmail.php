<?php

namespace App\Jobs;

use App\Dto\EnvelopeDto;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Service\Templid\TemplidService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TransactionalEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public int $tries = 3;

    /**
     * @param Address          $to
     * @param int              $templateId
     * @param Collection|null  $data
     * @param EnvelopeDto|null $envelopeDto
     */
    public function __construct(
        protected Address $to,
        protected int $templateId,
        protected ?Collection $data = null,
        protected ?EnvelopeDto $envelopeDto = null
    ) {}

    /**
     * @param TemplidService $templid
     *
     * @return void
     */
    public function handle(TemplidService $templid): void
    {
        $email = $templid->buildMailable(
            $this->templateId,
            $this->data,
            $this->envelopeDto
        );

        Mail::to($this->to)->send($email);
    }

    /**
     * @param Address          $to
     * @param int              $templateId
     * @param Collection|null  $data
     * @param EnvelopeDto|null $envelopeDto
     *
     * @return void
     */
    public static function send(
        Address $to,
        int $templateId,
        ?Collection $data = null,
        ?EnvelopeDto $envelopeDto = null
    ): void {
        self::dispatch($to, $templateId, $data, $envelopeDto);
    }
}
