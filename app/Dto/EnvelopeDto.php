<?php

declare(strict_types=1);

namespace App\Dto;

use Illuminate\Mail\Mailables\Address;

class EnvelopeDto
{
    /**
     * @param Address|null             $from
     * @param array<string, Address>   $to
     * @param array<string, Address>   $cc
     * @param array<string, Address>   $bcc
     * @param array<string, Address>   $replyTo
     * @param string[]                 $tags
     * @param array<mixed>             $metadata
     * @param \Closure|array<\Closure> $using
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
     * @return array<string, Address>
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return array<string, Address>
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @return array<string, Address>
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return array<string, Address>
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return array<mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return \Closure|array<\Closure>
     */
    public function getUsing(): \Closure|array
    {
        return $this->using;
    }
}
