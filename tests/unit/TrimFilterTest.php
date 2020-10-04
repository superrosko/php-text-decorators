<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\TrimFilter;
use UnitTester;

class TrimFilterTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var TrimFilter
     */
    public TrimFilter $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    protected function _before()
    {
        $filter = new EmptyFilter();
        $this->filter = new TrimFilter($filter);
        $this->fakerFactory = Factory::create();
    }

    public function testText()
    {
        $text = trim($this->fakerFactory->text);
        $this->assertEquals($text, $this->filter->format($text));
    }

    public function testTextWithExcludedChars()
    {
        $text = " \t\n\r\0\x0B".$this->fakerFactory->text." \t\n\r\0\x0B";
        $this->assertEquals(trim($text), $this->filter->format($text));

        $text = '___'.$this->fakerFactory->text.'___';
        $this->assertEquals(trim($text, '_'), $this->filter->setCharList('_')->format($text));
    }

    /**
     * @throws ReflectionException
     */
    public function testSetCharList()
    {
        $this->filter->setCharList('_');
        $this->tester->assertPrivatePropertyValue(
            '_',
            TrimFilter::class,
            'optionsCharList',
            $this->filter
        );
    }
}
