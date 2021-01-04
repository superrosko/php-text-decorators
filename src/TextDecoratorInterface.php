<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

interface TextDecoratorInterface
{
    /**
     * @param  string  $text
     *
     * @return string
     */
    public function format(string $text): string;
}
