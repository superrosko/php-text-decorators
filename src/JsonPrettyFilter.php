<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

final class JsonPrettyFilter implements TextDecoratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        return json_encode(json_decode($text), JSON_PRETTY_PRINT);
    }
}
