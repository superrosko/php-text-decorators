<?php

declare(strict_types=1);

namespace unit;

use Codeception\Test\Unit;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use ReflectionException;
use Superrosko\PhpTextDecorators\EmptyFilter;
use Superrosko\PhpTextDecorators\ExternalLinksFilter;
use UnitTester;

class ExternalLinksFilterTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected UnitTester $tester;

    /**
     * @var ExternalLinksFilter
     */
    public ExternalLinksFilter $filter;

    /**
     * @var Generator
     */
    public Generator $fakerFactory;

    protected function _before()
    {
        $filter = new EmptyFilter();
        $this->filter = new ExternalLinksFilter($filter);
        $this->fakerFactory = Factory::create();
    }

    public function testText()
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals($text, $this->filter->format($text));
    }

    public function testTextWithInternalLinks()
    {
        $text = $this->fakerFactory->text;
        $text .= ' <a href="http://example.com">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="https://example.com" class="test-class">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="https://example.com" class="test-class" rel="nofollow gallery">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="https://example.com" class="test-class" rel="nofollow gallery" target="_self">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="http://xn--e1afmkfd.xn--p1ai/test?param=test#hash" class="test-class" rel="nofollow gallery" target="_self">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="http://пример.рф/test?param=test#hash" class="test-class" rel="nofollow gallery" target="_self">test link</a> ';
        $text .= $this->fakerFactory->text;
        $text .= ' <a href="http://пример.рф/test?param=test#hash" class="test-class" rel="nofollow gallery" target="_self">test link</a> ';
        $text .= $this->fakerFactory->text;

        $this->filter->setExcludedHosts([
            'example.com',
            'xn--e1afmkfd.xn--p1ai',
            'пример.рф',
        ]);
        $this->assertEquals($text, $this->filter->format($text));
    }

    public function testCloseExternalLinks()
    {
        $text = $this->fakerFactory->text;
        $this->assertEquals(
            $text.' <a href="http://example.com" target="_blank" rel="nofollow noreferrer">test link</a> '.$text,
            $this->filter->closeExternalLinks($text.' <a href="http://example.com">test link</a> '.$text)
        );
        $this->assertEquals(
            $text.' <a href="http://example.com" target="_blank" rel="nofollow noreferrer">test link</a> '.$text.PHP_EOL
            .$text.' <a href="http://example.com" target="_blank" rel="nofollow noreferrer">test link</a> '.$text,
            $this->filter->closeExternalLinks($text.' <a href="http://example.com">test link</a> '.$text.PHP_EOL
                .$text.' <a href="http://example.com">test link</a> '.$text)
        );
        $this->assertEquals(
            $text.' <a href="http://example.com" target="_blank" rel="nofollow noreferrer">test link</a> '.$text
            .$text.' <a href="http://example.com" target="_blank" rel="nofollow noreferrer">test link</a> '.$text,
            $this->filter->closeExternalLinks($text.' <a href="http://example.com">test link</a> '.$text
                .$text.' <a href="http://example.com">test link</a> '.$text)
        );
        $this->assertEquals(
            $text.' <a href="http://example.com">test link</a> '.$text.PHP_EOL
            .$text.' <a href="http://example.net" target="_blank" rel="nofollow noreferrer">test link</a> '.$text,
            $this->filter->setExcludedHosts(['example.com'])->closeExternalLinks($text.' <a href="http://example.com">test link</a> '.$text.PHP_EOL
                .$text.' <a href="http://example.net">test link</a> '.$text)
        );
    }

    public function testPrepareLink()
    {
        $this->assertEquals(
            '<a href="https://example.com" target="_blank" rel="nofollow noreferrer">',
            $this->filter
                ->prepareLink(
                    '<a href="https://example.com">',
                    'href="https://example.com"',
                    'https://example.com'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com">',
            $this->filter
                ->setExcludedHosts(['example.com'])
                ->setRel(['nofollow', 'noreferrer'])
                ->setTarget('_blank')
                ->prepareLink(
                    '<a href="https://example.com">',
                    'href="https://example.com"',
                    'https://example.com'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com">',
            $this->filter
                ->setExcludedHosts([])
                ->setRel([])
                ->setTarget('')
                ->prepareLink(
                    '<a href="https://example.com">',
                    'href="https://example.com"',
                    'https://example.com'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" class="test-link" target="_blank" rel="nofollow noreferrer">',
            $this->filter
                ->setExcludedHosts([])
                ->setRel([])
                ->setTarget('')
                ->prepareLink(
                    '<a href="https://example.com" class="test-link" target="_blank" rel="nofollow noreferrer">',
                    'href="https://example.com"',
                    'https://example.com'
                )
        );
        $this->assertEquals(
            '<a href="https://example.net" class="test-link" target="_blank" rel="gallery nofollow noreferrer">',
            $this->filter
                ->setExcludedHosts(['example.com'])
                ->setRel(['nofollow', 'noreferrer'])
                ->setTarget('_blank')
                ->prepareLink(
                    '<a href="https://example.net" class="test-link" target="_self" rel="gallery">',
                    'href="https://example.net"',
                    'https://example.net'
                )
        );
    }

    public function testPrepareAttr()
    {
        $this->assertEquals(
            '<a href="https://example.com">',
            $this->filter->setRel([])
                ->setTarget('')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_REL,
                    '<a href="https://example.com">',
                    'href="https://example.com"'
                )
        );

        $this->assertEquals(
            '<a href="https://example.com" rel="nofollow noreferrer">',
            $this->filter->setRel(['nofollow', 'noreferrer'])
                ->setTarget('')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_REL,
                    '<a href="https://example.com">',
                    'href="https://example.com"'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" rel="gallery nofollow noreferrer">',
            $this->filter->setRel(['nofollow', 'noreferrer'])
                ->setTarget('')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_REL,
                    '<a href="https://example.com" rel="gallery">',
                    'href="https://example.com"'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" rel=\'gallery nofollow noreferrer\'>',
            $this->filter->setRel(['nofollow', 'noreferrer'])
                ->setTarget('')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_REL,
                    '<a href="https://example.com" rel="gallery">',
                    'href="https://example.com"',
                    '\''
                )
        );

        $this->assertEquals(
            '<a href="https://example.com" target="_blank">',
            $this->filter->setRel([])
                ->setTarget('_blank')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_TARGET,
                    '<a href="https://example.com">',
                    'href="https://example.com"'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" target="_self">',
            $this->filter->setRel([])
                ->setTarget('')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_TARGET,
                    '<a href="https://example.com" target="_self">',
                    'href="https://example.com"'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" target="_blank">',
            $this->filter->setRel([])
                ->setTarget('_blank')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_TARGET,
                    '<a href="https://example.com" target="_self">',
                    'href="https://example.com"'
                )
        );
        $this->assertEquals(
            '<a href="https://example.com" target=\'_blank\'>',
            $this->filter->setRel([])
                ->setTarget('_blank')
                ->prepareAttr(
                    ExternalLinksFilter::ATTR_TARGET,
                    '<a href="https://example.com" target="_self">',
                    'href="https://example.com"',
                    '\''
                )
        );

        $this->expectException(InvalidArgumentException::class);
        $this->filter->setRel([])
            ->setTarget('')
            ->prepareAttr(
                'test',
                '<a href="https://example.com">',
                'href="https://example.com"'
            );
    }

    public function testGetAttr()
    {
        $this->assertEquals('', $this->filter->getAttr('test'));

        $this->assertEquals('rel="nofollow noreferrer"', $this->filter->getAttr(ExternalLinksFilter::ATTR_REL));
        $this->assertEquals('', $this->filter->setRel([])->getAttr(ExternalLinksFilter::ATTR_REL));
        $this->assertEquals(
            'rel="nofollow noreferrer"',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getAttr(ExternalLinksFilter::ATTR_REL)
        );
        $this->assertEquals('', $this->filter->setRel([])->getAttr(ExternalLinksFilter::ATTR_REL));
        $this->assertEquals(
            'rel="nofollow noreferrer"',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getAttr(ExternalLinksFilter::ATTR_REL)
        );
        $this->assertEquals(
            'rel="gallery nofollow noreferrer"',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getAttr(
                ExternalLinksFilter::ATTR_REL,
                'nofollow gallery'
            )
        );
        $this->assertEquals(
            'rel=\'gallery nofollow noreferrer\'',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getAttr(
                ExternalLinksFilter::ATTR_REL,
                'nofollow gallery',
                '\''
            )
        );
        $this->assertEquals(
            'rel="gallery nofollow"',
            $this->filter->setRel([])->getAttr(ExternalLinksFilter::ATTR_REL, 'nofollow gallery')
        );
        $this->assertEquals(
            'rel=\'gallery nofollow\'',
            $this->filter->setRel([])->getAttr(ExternalLinksFilter::ATTR_REL, 'nofollow gallery', '\'')
        );

        $this->assertEquals('target="_blank"', $this->filter->getAttr(ExternalLinksFilter::ATTR_TARGET));
        $this->assertEquals('', $this->filter->setTarget('')->getAttr(ExternalLinksFilter::ATTR_TARGET));
        $this->assertEquals(
            'target="_blank"',
            $this->filter->setTarget('_blank')->getAttr(ExternalLinksFilter::ATTR_TARGET)
        );
        $this->assertEquals(
            'target=\'_blank\'',
            $this->filter->setTarget('_blank')->getAttr(ExternalLinksFilter::ATTR_TARGET, '', '\'')
        );
        $this->assertEquals(
            'target="_blank"',
            $this->filter->setTarget('')->getAttr(ExternalLinksFilter::ATTR_TARGET, '_blank')
        );
        $this->assertEquals(
            'target=\'_blank\'',
            $this->filter->setTarget('')->getAttr(ExternalLinksFilter::ATTR_TARGET, '_blank', '\'')
        );
        $this->assertEquals(
            'target="_blank"',
            $this->filter->setTarget('_blank')->getAttr(ExternalLinksFilter::ATTR_TARGET, '_self')
        );
        $this->assertEquals(
            'target=\'_blank\'',
            $this->filter->setTarget('_blank')->getAttr(ExternalLinksFilter::ATTR_TARGET, '_self', '\'')
        );
    }

    public function testGetRel()
    {
        $this->assertEquals('rel="nofollow noreferrer"', $this->filter->getRel());
        $this->assertEquals('', $this->filter->setRel([])->getRel());
        $this->assertEquals('rel="nofollow noreferrer"', $this->filter->setRel(['nofollow', 'noreferrer'])->getRel());
        $this->assertEquals(
            'rel="gallery nofollow noreferrer"',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getRel('nofollow gallery')
        );
        $this->assertEquals(
            'rel=\'gallery nofollow noreferrer\'',
            $this->filter->setRel(['nofollow', 'noreferrer'])->getRel('nofollow gallery', '\'')
        );
        $this->assertEquals('rel="gallery nofollow"', $this->filter->setRel([])->getRel('nofollow gallery'));
        $this->assertEquals('rel=\'gallery nofollow\'', $this->filter->setRel([])->getRel('nofollow gallery', '\''));
    }

    public function testGetTarget()
    {
        $this->assertEquals('target="_blank"', $this->filter->getTarget());
        $this->assertEquals('', $this->filter->setTarget('')->getTarget());
        $this->assertEquals('target="_blank"', $this->filter->setTarget('_blank')->getTarget());
        $this->assertEquals('target=\'_blank\'', $this->filter->setTarget('_blank')->getTarget('', '\''));
        $this->assertEquals('target="_blank"', $this->filter->setTarget('')->getTarget('_blank'));
        $this->assertEquals('target=\'_blank\'', $this->filter->setTarget('')->getTarget('_blank', '\''));
        $this->assertEquals('target="_blank"', $this->filter->setTarget('_blank')->getTarget('_self'));
        $this->assertEquals('target=\'_blank\'', $this->filter->setTarget('_blank')->getTarget('_self', '\''));
    }

    public function testIsExternalLink()
    {
        $this->filter->setExcludedHosts([
            'example.com',
            'xn--e1afmkfd.xn--p1ai',
            'пример.рф',
        ]);

        $this->assertFalse($this->filter->isExternalLink('http://example.com'));
        $this->assertFalse($this->filter->isExternalLink('http://xn--e1afmkfd.xn--p1ai'));
        $this->assertFalse($this->filter->isExternalLink('http://пример.рф'));
        $this->assertFalse($this->filter->isExternalLink('http://example.com/test?param=test#hash'));
        $this->assertFalse($this->filter->isExternalLink('http://xn--e1afmkfd.xn--p1ai/test?param=test#hash'));
        $this->assertFalse($this->filter->isExternalLink('http://пример.рф/test?param=test#hash'));

        $this->assertTrue($this->filter->isExternalLink('http://example.net'));
        $this->assertTrue($this->filter->isExternalLink('http://example.net/test?param=test#hash'));

        $this->filter->setExcludedHosts([]);

        $this->assertTrue($this->filter->isExternalLink('http://example.com'));
        $this->assertTrue($this->filter->isExternalLink('http://xn--e1afmkfd.xn--p1ai'));
        $this->assertTrue($this->filter->isExternalLink('http://пример.рф'));
        $this->assertTrue($this->filter->isExternalLink('http://example.com/test?param=test#hash'));
        $this->assertTrue($this->filter->isExternalLink('http://xn--e1afmkfd.xn--p1ai/test?param=test#hash'));
        $this->assertTrue($this->filter->isExternalLink('http://пример.рф/test?param=test#hash'));
    }

    /**
     * @throws ReflectionException
     */
    public function testSetExcludedHosts()
    {
        $this->filter->setExcludedHosts(['example.com']);
        $this->tester->assertPrivatePropertyValue(
            ['example.com'],
            ExternalLinksFilter::class,
            'optionsExcludedHosts',
            $this->filter
        );

        $this->filter->setExcludedHosts([]);
        $this->tester->assertPrivatePropertyValue(
            [],
            ExternalLinksFilter::class,
            'optionsExcludedHosts',
            $this->filter
        );

        $this->filter->setExcludedHosts(['example.com', 'www.example.com']);
        $this->tester->assertPrivatePropertyValue(
            ['example.com', 'www.example.com'],
            ExternalLinksFilter::class,
            'optionsExcludedHosts',
            $this->filter
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetRel()
    {
        $this->filter->setRel(['test']);
        $this->tester->assertPrivatePropertyValue(
            ['test'],
            ExternalLinksFilter::class,
            'optionsRel',
            $this->filter
        );

        $this->filter->setRel([]);
        $this->tester->assertPrivatePropertyValue(
            [],
            ExternalLinksFilter::class,
            'optionsRel',
            $this->filter
        );

        $this->filter->setRel(['nofollow', 'noreferrer']);
        $this->tester->assertPrivatePropertyValue(
            ['nofollow', 'noreferrer'],
            ExternalLinksFilter::class,
            'optionsRel',
            $this->filter
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testSetTarget()
    {
        $this->filter->setTarget('_blank');
        $this->tester->assertPrivatePropertyValue(
            '_blank',
            ExternalLinksFilter::class,
            'optionsTarget',
            $this->filter
        );

        $this->filter->setTarget('_self');
        $this->tester->assertPrivatePropertyValue(
            '_self',
            ExternalLinksFilter::class,
            'optionsTarget',
            $this->filter
        );

        $this->filter->setTarget('_parent');
        $this->tester->assertPrivatePropertyValue(
            '_parent',
            ExternalLinksFilter::class,
            'optionsTarget',
            $this->filter
        );

        $this->filter->setTarget('_top');
        $this->tester->assertPrivatePropertyValue(
            '_top',
            ExternalLinksFilter::class,
            'optionsTarget',
            $this->filter
        );

        $this->filter->setTarget('');
        $this->tester->assertPrivatePropertyValue(
            '',
            ExternalLinksFilter::class,
            'optionsTarget',
            $this->filter
        );

        $this->expectException(InvalidArgumentException::class);
        $this->filter->setTarget('test');
    }
}
