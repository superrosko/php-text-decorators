<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

use ParsedownExtra;

final class ParsedownExtraFilter extends TextDecorator
{
    /**
     * @var ParsedownExtra
     */
    private ParsedownExtra $parsedownExtra;

    public function __construct(TextDecoratorInterface $textDecorator)
    {
        $this->parsedownExtra = new ParsedownExtra();

        parent::__construct($textDecorator);
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return $this->parsedownExtra->text($text);
    }

    /**
     * @param  bool  $param
     * @return $this
     */
    public function setBreaksEnabled(bool $param): ParsedownExtraFilter
    {
        $this->parsedownExtra->setBreaksEnabled($param);

        return $this;
    }

    /**
     * @param  bool  $param
     * @return $this
     */
    public function setMarkupEscaped(bool $param): ParsedownExtraFilter
    {
        $this->parsedownExtra->setMarkupEscaped($param);

        return $this;
    }

    /**
     * @param  bool  $param
     * @return $this
     */
    public function setSafeMode(bool $param): ParsedownExtraFilter
    {
        $this->parsedownExtra->setSafeMode($param);

        return $this;
    }

    /**
     * @param  bool  $param
     * @return $this
     */
    public function setUrlsLinked(bool $param): ParsedownExtraFilter
    {
        $this->parsedownExtra->setUrlsLinked($param);

        return $this;
    }
}
