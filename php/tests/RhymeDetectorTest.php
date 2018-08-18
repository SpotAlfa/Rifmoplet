<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet\Tests;

use SpotAlfa\Rifmoplet\RhymeDetector;
use PHPUnit\Framework\TestCase;
use SpotAlfa\Rifmoplet\TextIterator;

class RhymeDetectorTest extends TestCase
{

    /**
     * @dataProvider configProvider
     *
     * @param int $settings
     * @param bool $expected
     */
    public function testConfigure(int $settings, bool $expected)
    {
        $first = new TextIterator('irItikOf i kO', 4);
        $second = new TextIterator('fEriti f nOfij kOt', 4);
        $detector = new RhymeDetector($first, $second);

        $first->next();
        $second->next();
        $detector->configure($settings);
        $actual = $detector->isRhyme();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider rhymesProvider
     *
     * @param TextIterator $first
     * @param TextIterator $second
     * @param bool $expected
     */
    public function testIsRhyme(TextIterator $first, TextIterator $second, bool $expected): void
    {
        $detector = new RhymeDetector($first, $second);

        $actual = $detector->isRhyme();

        $this->assertEquals($expected, $actual);
    }

    public function rhymesProvider(): array
    {
        $args = [
            [new TextIterator('tEtstfe mIk', 3), new TextIterator('tEfstfennIk', 3), true],
            [new TextIterator('atnAhti prasn\'Ohsa', 6), new TextIterator('kanqAitsa tEtstfa', 6), false],
            [new TextIterator('masafAka', 4), new TextIterator('stA sarakA', 4), false],
            [new TextIterator('irItikOf i kO', 4), new TextIterator('fEriti f nOfij kOt', 4), false]
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $args[3][0]->next();
        /** @noinspection PhpUndefinedMethodInspection */
        $args[3][1]->next();

        return $args;
    }

    public function configProvider(): array
    {
        return [
            [0, false],
            [RhymeDetector::ACCENT_SHIFT, true]
        ];
    }
}
