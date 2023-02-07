<?php

declare(strict_types=1);

namespace App\Dto;

class TemplidTemplateDto
{
    /**
     * @param string $subject
     * @param string $html
     * @param string $text
     */
    public function __construct(
        protected string $subject = '',
        protected string $html    = '',
        protected string $text    = '',
    ) {}

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
