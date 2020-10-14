<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\LazySizesFilter;
use UnitTester;

class LazySizesFilterTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var LazySizesFilter
     */
    public LazySizesFilter $filter;

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
        $this->filter = new LazySizesFilter($filter);
        $this->fakerFactory = Factory::create();
    }

    public function testText(): void
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals($text, $this->filter->format($text));
    }

    public function testTextWithCommonImages(): void
    {
        $text = $this->fakerFactory->text;
        $text .= ' <img src="http://example.com/test.jpg" class="test" alt="test" /> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <img src="http://example.com/test.jpg" class="test" alt="" /> ';
        $text .= $this->fakerFactory->text;

        $this->assertEquals($text, $this->filter->format($text));
    }

    public function testProcessLazyloadImg(): void
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals(
            $text.' <img data-src="/test.jpg" alt="test" class="test lazyload" /> '.$text,
            $this->filter->processLazyloadImg($text.' <img src="/test.jpg" alt="test" class="test lazyload" /> '.$text)
        );
    }

    public function testPrepareImg(): void
    {
        $this->assertEquals(
            '<img src="http://example.com/test.jpg" alt="test" />',
            $this->filter
                ->prepareImg(
                    '<img src="http://example.com/test.jpg" alt="test" />',
                    'src="http://example.com/test.jpg"',
                    'http://example.com/test.jpg'
                )
        );
        $this->assertEquals(
            '<img src="http://example.com/test.jpg" alt="test" class="test" />',
            $this->filter
                ->prepareImg(
                    '<img src="http://example.com/test.jpg" alt="test" class="test" />',
                    'src="http://example.com/test.jpg"',
                    'http://example.com/test.jpg'
                )
        );
        $this->assertEquals(
            '<img data-src="http://example.com/test.jpg" alt="test" class="lazyload" />',
            $this->filter
                ->prepareImg(
                    '<img src="http://example.com/test.jpg" alt="test" class="lazyload" />',
                    'src="http://example.com/test.jpg"',
                    'http://example.com/test.jpg'
                )
        );
    }

    public function testIsLazyloadClass(): void
    {
        $this->assertFalse($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" />'));
        $this->assertFalse($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="test" />'));
        $this->assertTrue($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="lazyload" />'));
        $this->assertTrue($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="test lazyload" />'));

        $this->filter->setClass('lazyload-test');

        $this->assertFalse($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" />'));
        $this->assertFalse($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="test" />'));
        $this->assertFalse($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="lazyload" />'));
        $this->assertTrue($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="test lazyload-test" />'));
        $this->assertTrue($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="lazyload-test" />'));
        $this->assertTrue($this->filter->isLazyloadClass('<img src="http://example.com/test.jpg" alt="test" class="test lazyload-test" />'));
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testSetClass(): void
    {
        $this->filter->setClass('lazyload');
        $this->tester->assertPrivatePropertyValue(
            'lazyload',
            LazySizesFilter::class,
            'optionsClass',
            $this->filter
        );

        $this->filter->setClass('test');
        $this->tester->assertPrivatePropertyValue(
            'test',
            LazySizesFilter::class,
            'optionsClass',
            $this->filter
        );
    }
}
