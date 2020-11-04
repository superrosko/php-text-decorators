<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Highlight\Highlighter;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\HighlighterFilter;

class HighlighterFilterTest extends Unit
{
    /**
     * @var HighlighterFilter
     */
    public HighlighterFilter $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    /**
     * @var Highlighter
     */
    private Highlighter $highlighter;

    /**
     * @return void
     */
    protected function _before()
    {
        $filter = new EmptyFilter();
        $this->filter = new HighlighterFilter($filter);
        $this->fakerFactory = Factory::create();
        $this->highlighter = new Highlighter();
    }

    public function testText(): void
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals($text, $this->filter->format($text));
    }

    /**
     * @throws Exception
     */
    public function testTextWithCode(): void
    {
        $code = '<?php phpinfo(); ?>';
        $textCode = '<pre><code class="language-php">'.$code.'</code></pre>';
        $textCodeHighlighted = (string) $this->highlighter->highlight('php', $code)->value;

        $this->assertStringContainsString($textCodeHighlighted, $this->filter->format($textCode));
    }

    /**
     * @throws Exception
     */
    public function testTextWithMultilineCode(): void
    {
        $code = '<?php
        phpinfo(); ?>';
        $textCode = '<pre><code class="language-php">'.$code.'</code></pre>';
        $textCodeHighlighted = (string) $this->highlighter->highlight('php', $code)->value;

        $this->assertStringContainsString($textCodeHighlighted, $this->filter->format($textCode));
    }

    /**
     * @throws Exception
     */
    public function testHighlightContent(): void
    {
        $code = '<?php phpinfo(); ?>';
        $textCode = '<pre><code class="language-php">'.$code.'</code></pre>';
        $textCodeHighlighted = (string) $this->highlighter->highlight('php', $code)->value;
        $this->assertStringContainsString($textCodeHighlighted, $this->filter->highlightContent($textCode));
    }

    /**
     * @throws Exception
     */
    public function testPrepareCode(): void
    {
        $code = '<?php phpinfo(); ?>';
        $textCodeHighlighted = (string) $this->highlighter->highlight('php', $code)->value;
        $this->assertStringContainsString($textCodeHighlighted, $this->filter->prepareCode('php', $code));
        $this->assertEquals(
            '<pre><code class="hljs">&lt;?php phpinfo(); ?&gt;</code></pre>',
            $this->filter->prepareCode('test-exception', $code)
        );
    }
}
