<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

final class TrimFilter extends TextDecorator
{
    /**
     * @var string
     */
    private string $optionsCharList = " \t\n\r\0\x0B";

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        return trim($text, $this->optionsCharList);
    }

    public function setCharList(string $param = " \t\n\r\0\x0B"): TrimFilter
    {
        $this->optionsCharList = $param;

        return $this;
    }
}
