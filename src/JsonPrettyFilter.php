<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

final class JsonPrettyFilter extends TextDecorator
{
    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return (string) json_encode(json_decode($text), JSON_PRETTY_PRINT);
    }
}
