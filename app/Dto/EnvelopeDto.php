<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Mail\Mailables\Address;

class EnvelopeDto
{
    /**
     * @param Address|null   $from
     * @param array          $to
     * @param array          $cc
     * @param array          $bcc
     * @param array          $replyTo
     * @param array          $tags
     * @param array          $metadata
     * @param \Closure|array $using
     */
    public function __construct(
        protected Address|null   $from     = null,
        protected array          $to       = [],
        protected array          $cc       = [],
        protected array          $bcc      = [],
        protected array          $replyTo  = [],
        protected array          $tags     = [],
        protected array          $metadata = [],
        protected \Closure|array $using    = [],
    ) {}

    /**
     * @return Address|null
     */
    public function getFrom(): ?Address
    {
        return $this->from;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return array
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return array
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return \Closure|array
     */
    public function getUsing(): \Closure|array
    {
        return $this->using;
    }
}
