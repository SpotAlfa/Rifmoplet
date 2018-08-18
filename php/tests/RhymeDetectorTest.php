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

/**
 * Tests {@see RhymeDetector}.
 *
 * @package SpotAlfa\Rifmoplet\Tests
 */
class RhymeDetectorTest extends TestCase
{

    /**
     * Tests {@see RhymeDetector} configuration.
     *
     * @dataProvider configProvider
     *
     * @param int $settings config for detector
     * @param bool $expected expected result
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
     * Tests if {@see RhymeDetector} correctly detects rhymes.
     *
     * @dataProvider rhymesProvider
     *
     * @param TextIterator $first first substring container
     * @param TextIterator $second second substring container
     * @param bool $expected expected result
     */
    public function testIsRhyme(TextIterator $first, TextIterator $second, bool $expected): void
    {
        $detector = new RhymeDetector($first, $second);

        $actual = $detector->isRhyme();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Provides {@see TextIterator}s and expected {@see RhymeDetector::isRhyme()} results for test.
     *
     * @return array args for {@see RhymeDetectorTest::testIsRhyme()}
     */
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

    /**
     * Provides configurations for {@see RhymeDetector} and expected {@see RhymeDetector::isRhyme()} results.
     *
     * @return array args for {@see RhymeDetectorTest::testConfigure()}
     */
    public function configProvider(): array
    {
        return [
            [0, false],
            [RhymeDetector::ACCENT_SHIFT, true]
        ];
    }
}
