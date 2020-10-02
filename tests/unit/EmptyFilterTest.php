<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use Superrosko\PhpTextDecorators\EmptyFilter;

class EmptyFilterTest extends Unit
{
    /**
     * @var EmptyFilter
     */
    public EmptyFilter $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    protected function _before()
    {
        $this->filter = new EmptyFilter();
        $this->fakerFactory = Factory::create();
    }

    public function testText()
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals($text, $this->filter->format($text));
    }
}
