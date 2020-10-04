<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

use InvalidArgumentException;

final class ExternalLinksFilter extends TextDecorator
{
    const ATTR_REL = 'rel';
    const ATTR_TARGET = 'target';

    /**
     * @var array|string[]
     */
    private array $allowAttr = [
        self::ATTR_REL,
        self::ATTR_TARGET,
    ];

    /**
     * @var string[]
     */
    private array $optionsExcludedHosts = [];

    /**
     * @var array
     */
    private array $optionsRel = ['nofollow', 'noreferrer'];

    /**
     * @var string
     */
    private string $optionsTarget = '_blank';

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return $this->closeExternalLinks($text);
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function closeExternalLinks(string $text): string
    {
        $result = preg_replace_callback(
            '~(?<full_tag><a(?:.*?)(?<href>href\s*?=\s*?(?<quote>["\'])(?<url>.+?)\k<quote>)(?:.*?)?\s*?>)~iu',
            fn ($matches) => $this->prepareLink(
                $matches['full_tag'],
                $matches['href'],
                $matches['url'],
                $matches['quote']
            ),
            $text
        );

        return $result ?? $text;
    }

    /**
     * @param  string  $fullTag
     * @param  string  $url
     * @param  string  $href
     * @param  string  $quote
     * @return string
     */
    public function prepareLink(string $fullTag, string $href, string $url, string $quote = '"'): string
    {
        if ($this->isExternalLink($url)) {
            $fullTag = $this->prepareAttr(self::ATTR_REL, $fullTag, $href, $quote);
            $fullTag = $this->prepareAttr(self::ATTR_TARGET, $fullTag, $href, $quote);
        }

        return $fullTag;
    }

    /**
     * @param  string  $attrName
     * @param  string  $fullTag
     * @param  string  $href
     * @param  string  $quote
     * @return string
     */
    public function prepareAttr(string $attrName, string $fullTag, string $href, string $quote = '"'): string
    {
        if (! in_array($attrName, $this->allowAttr)) {
            throw new InvalidArgumentException('Wrong attr name.', 0);
        }

        if (preg_match(
            '~(?:.*)(?<full_attr>'.$attrName.'\s*?=\s*?["\'](?<attr_params>(?:.*?))["\'])~iu',
            $fullTag,
            $matches
        )) {
            $attr = $this->getAttr($attrName, $matches['attr_params'], $quote);

            return str_replace($matches['full_attr'], $attr, $fullTag);
        }

        $attr = $this->getAttr($attrName, '', $quote);

        return str_replace($href, trim($href.' '.$attr), $fullTag);
    }

    /**
     * @param  string  $attrName
     * @param  string  $attrParams
     * @param  string  $quote
     * @return string
     */
    public function getAttr(string $attrName, string $attrParams = '', string $quote = '"'): string
    {
        switch ($attrName) {
            case self::ATTR_REL:
                return $this->getRel($attrParams, $quote);
            case self::ATTR_TARGET:
                return $this->getTarget($attrParams, $quote);
        }

        return '';
    }

    /**
     * @param  string  $attrParams
     * @param  string  $quote
     * @return string
     */
    public function getRel(string $attrParams = '', string $quote = '"'): string
    {
        $attrParams = explode(' ', $attrParams);
        $attrParams = array_unique(array_merge($this->optionsRel, $attrParams));
        $attrParams = array_filter($attrParams, fn ($el) => ! empty($el));
        sort($attrParams);
        $attrParams = implode(' ', $attrParams);

        if (! empty($attrParams)) {
            return 'rel='.$quote.trim($attrParams).$quote;
        }

        return '';
    }

    /**
     * @param  string  $attrParams
     * @param  string  $quote
     * @return string
     */
    public function getTarget(string $attrParams = '', string $quote = '"'): string
    {
        if (! empty($this->optionsTarget)) {
            return 'target='.$quote.trim($this->optionsTarget).$quote;
        } elseif (! empty($attrParams)) {
            return 'target='.$quote.trim($attrParams).$quote;
        }

        return '';
    }

    /**
     * @param  string  $url
     * @return bool
     */
    public function isExternalLink(string $url): bool
    {
        $parsedUrl = parse_url($url);
        $urlHost = $parsedUrl['host'] ?? null;

        return ! in_array($urlHost, $this->optionsExcludedHosts);
    }

    /**
     * @param  array  $optionsExcludedHosts
     * @return ExternalLinksFilter
     */
    public function setExcludedHosts(array $optionsExcludedHosts = []): ExternalLinksFilter
    {
        $this->optionsExcludedHosts = $optionsExcludedHosts;

        return $this;
    }

    /**
     * @param  string[]  $param
     * @return ExternalLinksFilter
     */
    public function setRel(array $param = ['nofollow', 'noreferrer']): ExternalLinksFilter
    {
        $this->optionsRel = $param;

        return $this;
    }

    /**
     * @param  string  $param
     * @return ExternalLinksFilter
     */
    public function setTarget(string $param = '_blank'): ExternalLinksFilter
    {
        $whiteList = ['_blank', '_self', '_parent', '_top', ''];
        if (! in_array($param, $whiteList)) {
            throw new InvalidArgumentException('Wrong target parameter value.', 0);
        }

        $this->optionsTarget = $param;

        return $this;
    }
}
