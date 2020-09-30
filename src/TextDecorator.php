<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

class TextDecorator implements TextDecoratorInterface
{
    /**
     * @var TextDecoratorInterface
     */
    protected TextDecoratorInterface $textDecorator;

    /**
     * TextDecorator constructor.
     * @param  TextDecoratorInterface  $textDecorator
     */
    public function __construct(TextDecoratorInterface $textDecorator)
    {
        $this->textDecorator = $textDecorator;
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function format(string $text): string
    {
        return $this->textDecorator->format($text);
    }
}
