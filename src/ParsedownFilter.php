<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

use Parsedown;

final class ParsedownFilter extends TextDecorator
{
    /**
     * @var Parsedown
     */
    private Parsedown $parsedown;

    public function __construct(TextDecoratorInterface $textDecorator, Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;

        parent::__construct($textDecorator);
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return (string) $this->parsedown->text($text);
    }

    /**
     * @param  bool  $param
     *
     * @return $this
     */
    public function setBreaksEnabled(bool $param): ParsedownFilter
    {
        $this->parsedown->setBreaksEnabled($param);

        return $this;
    }

    /**
     * @param  bool  $param
     *
     * @return $this
     */
    public function setMarkupEscaped(bool $param): ParsedownFilter
    {
        $this->parsedown->setMarkupEscaped($param);

        return $this;
    }

    /**
     * @param  bool  $param
     *
     * @return $this
     */
    public function setSafeMode(bool $param): ParsedownFilter
    {
        $this->parsedown->setSafeMode($param);

        return $this;
    }

    /**
     * @param  bool  $param
     *
     * @return $this
     */
    public function setUrlsLinked(bool $param): ParsedownFilter
    {
        $this->parsedown->setUrlsLinked($param);

        return $this;
    }
}
