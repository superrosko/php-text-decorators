<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use Superrosko\PhpTextDecorators\TextDecorator;
use Superrosko\PhpTextDecorators\TextDecoratorInterface;

class TextDecoratorTest extends Unit
{
    /**
     * @var TextDecorator
     */
    public TextDecorator $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    /**
     * @var MockObject
     */
    public MockObject $stub;

    protected function _before()
    {
        $this->stub = $this->createMock(TextDecoratorInterface::class);
        $this->filter = new TextDecorator($this->stub);
        $this->fakerFactory = Factory::create();
    }

    public function testText()
    {
        $text = $this->fakerFactory->text;
        $this->stub->method('format')
            ->willReturn($text);
        $this->assertEquals($text, $this->filter->format($text));
    }
}
