<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

final class EmptyFilter implements TextDecoratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        return $text;
    }
}
