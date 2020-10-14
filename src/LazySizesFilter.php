<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

final class LazySizesFilter extends TextDecorator
{
    /**
     * @var string
     */
    private string $optionsClass = 'lazyload';

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return $this->processLazyloadImg($text);
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function processLazyloadImg(string $text): string
    {
        $result = preg_replace_callback(
            '~(?<full_tag><img(?:.*?)(?<src>src\s*?=\s*?(?<quote>["\'])(?<url>.+?)\k<quote>)(?:.*?)?\s*?>)~iu',
            fn (array $matches) => $this->prepareImg(
                (string) $matches['full_tag'],
                (string) $matches['src'],
                (string) $matches['url'],
                (string) $matches['quote']
            ),
            $text
        );

        return $result ?? $text;
    }

    /**
     * @param  string  $fullTag
     * @param  string  $url
     * @param  string  $src
     * @param  string  $quote
     * @return string
     */
    public function prepareImg(string $fullTag, string $src, string $url, string $quote = '"'): string
    {
        if ($this->isLazyloadClass($fullTag)) {
            $fullTag = str_replace($src, 'data-src='.$quote.$url.$quote, $fullTag);
        }

        return $fullTag;
    }

    /**
     * @param  string  $fullTag
     * @return bool
     */
    public function isLazyloadClass(string $fullTag): bool
    {
        if (preg_match(
            '~(?:.*)(?:class\s*?=\s*?["\'](?<attr_params>(?:.*?))["\'])~iu',
            $fullTag,
            $matches
        )) {
            $attrParams = explode(' ', $matches['attr_params']);

            return in_array($this->optionsClass, $attrParams);
        }

        return false;
    }

    /**
     * @param  string  $param
     * @return LazySizesFilter
     */
    public function setClass(string $param = 'lazyload'): LazySizesFilter
    {
        $this->optionsClass = $param;

        return $this;
    }
}
