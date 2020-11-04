<?php

declare(strict_types=1);

namespace Superrosko\PhpTextDecorators;

use Exception;
use Highlight\Highlighter;

final class HighlighterFilter extends TextDecorator
{
    /**
     * @var Highlighter
     */
    private Highlighter $highlighter;

    public function __construct(TextDecoratorInterface $textDecorator)
    {
        $this->highlighter = new Highlighter();

        parent::__construct($textDecorator);
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $text): string
    {
        $text = parent::format($text);

        return $this->highlightContent($text);
    }

    /**
     * @param  string  $text
     * @return string
     */
    public function highlightContent(string $text): string
    {
        $result = preg_replace_callback(
            '~<pre>\s*<code(?:.*?)class="language-(?<language>.*?)"(?:.*?)>\s*(?<code>.*?)\s*</code>\s*</pre>~ius',
            fn (array $matches) => $this->prepareCode(
                (string) $matches['language'],
                (string) $matches['code'],
            ),
            $text
        );

        return $result ?? $text;
    }

    /**
     * @param  string  $language
     * @param  string  $code
     * @return string
     */
    public function prepareCode(string $language, string $code): string
    {
        $result = '';
        try {
            $code = htmlspecialchars_decode($code);
            $highlighted = $this->highlighter->highlight($language, $code);
            $result .= '<pre><code class="hljs '.(string) $highlighted->language.'">';
            $result .= (string) $highlighted->value;
            $result .= '</code></pre>';
        } catch (Exception $e) {
            $result .= '<pre><code class="hljs">';
            $result .= htmlentities($code);
            $result .= '</code></pre>';
        }

        return $result;
    }
}
