<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\JsonPrettyFilter;
use UnitTester;

class JsonPrettyFilterTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var JsonPrettyFilter
     */
    public JsonPrettyFilter $filter;

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
        $this->filter = new JsonPrettyFilter($filter);
        $this->fakerFactory = Factory::create();
    }

    public function testText(): void
    {
        $text = $this->fakerFactory->text;
        $this->assertNull(null, $this->filter->format($text));
    }

    public function testTextWithExcludedChars(): void
    {
        $text = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        $this->assertJsonStringEqualsJsonString($text, $this->filter->format($text));
        $this->assertEquals(json_encode(json_decode($text), JSON_PRETTY_PRINT), $this->filter->format($text));
    }
}
