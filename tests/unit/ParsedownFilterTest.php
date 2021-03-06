<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use ParsedownExtra;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\ParsedownFilter;

class ParsedownFilterTest extends Unit
{
    /**
     * @var ParsedownFilter
     */
    public ParsedownFilter $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    /**
     * @return void
     */
    protected function _before()
    {
        $filter = new EmptyFilter();
        $parsedown = new ParsedownExtra();
        $this->filter = new ParsedownFilter($filter, $parsedown);
        $this->fakerFactory = Factory::create();
    }

    public function testText(): void
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals('<p>'.$text.'</p>', $this->filter->format($text));
    }

    public function testImage(): void
    {
        $text = '![Image](https://example.com/test.jpg) {.test-class}';
        $this->assertEquals(
            '<p><img src="https://example.com/test.jpg" alt="Image" class="test-class" /></p>',
            $this->filter->format($text)
        );
    }

    public function testLink(): void
    {
        $text = '[Link](https://example.com/test/) {.test-class}';
        $this->assertEquals(
            '<p><a href="https://example.com/test/" class="test-class">Link</a></p>',
            $this->filter->format($text)
        );
    }

    public function testHeader(): void
    {
        $text = '# Header {#anchor-header}';
        $this->assertEquals('<h1 id="anchor-header">Header</h1>', $this->filter->format($text));
    }

    public function testCode(): void
    {
        $text = '```<?php phpinfo(); ?>```';
        $this->assertEquals('<p><code>&lt;?php phpinfo(); ?&gt;</code></p>', $this->filter->format($text));
    }

    public function testSetBreaksEnabled(): void
    {
        $text = "1st line \n 2nd line";
        $this->assertEquals(
            "<p>1st line<br />\n2nd line</p>",
            $this->filter->setBreaksEnabled(true)->format($text)
        );
        $this->assertEquals("<p>1st line\n2nd line</p>", $this->filter->setBreaksEnabled(false)->format($text));
    }

    public function testSetMarkupEscaped(): void
    {
        $text = '<div>*test*</div>';
        $this->assertEquals(
            '<p>&lt;div&gt;<em>test</em>&lt;/div&gt;</p>',
            $this->filter->setMarkupEscaped(true)->format($text)
        );
        $this->assertEquals('<div>*test*</div>', $this->filter->setMarkupEscaped(false)->format($text));
    }

    public function testSetSafeMode(): void
    {
        $text = '<script>alert("xss");</script>';
        $this->assertEquals(
            '<p>&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;</p>',
            $this->filter->setSafeMode(true)->format($text)
        );
        $this->assertEquals('<script>alert("xss");</script>', $this->filter->setSafeMode(false)->format($text));
    }

    public function testSetUrlsLinked(): void
    {
        $text = 'https://example.com/test/';
        $this->assertEquals(
            '<p><a href="https://example.com/test/">https://example.com/test/</a></p>',
            $this->filter->setUrlsLinked(true)->format($text)
        );
        $this->assertEquals('<p>https://example.com/test/</p>', $this->filter->setUrlsLinked(false)->format($text));
    }
}
