<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet\Tests;

use SpotAlfa\Rifmoplet\TextIterator;
use PHPUnit\Framework\TestCase;

/**
 * Tests TextIterator.
 *
 * @package SpotAlfa\Rifmoplet\Tests
 */
class TextIteratorTest extends TestCase
{
    /** @var string text to iterate through */
    private const TEXT = 'fsO piriplitinO mOri nItij no';

    /** @var TextIterator iterator instance */
    private $stub;

    /**
     * Creates iterator instance.
     */
    public function setUp(): void
    {
        $this->stub = new TextIterator(self::TEXT, 3);
    }

    /**
     * Tests if the iterator correctly searches previous char matching callback.
     *
     * @dataProvider prevProvider
     *
     * @param int $next number of iterations
     * @param int $expected expected string index
     */
    public function testPrevious(int $next, int $expected): void
    {
        $match = function (string $char): bool {
            return (bool)preg_match('/[AEOIU]/', $char);
        };

        $this->stub->rewind();
        for ($i = 0; $i < $next; $i++) {
            $this->stub->next();
        }
        $actual = $this->stub->key() - $this->stub->previous($match);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if the iterator correctly searches the following char matching callback.
     *
     * @dataProvider followingProvider
     *
     * @param int $next number of iterations
     * @param int $expected expected string index
     */
    public function testFollowing(int $next, int $expected): void
    {
        $match = function (string $char): bool {
            return (bool)preg_match('/[AEOIU]/', $char);
        };

        $this->stub->rewind();
        for ($i = 0; $i < $next; $i++) {
            $this->stub->next();
        }
        $actual = $this->stub->current() + $this->stub->following($match);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests if the iterator returns valid `foreach` loop values.
     */
    public function testCurrent(): void
    {
        $expectedValues = [7, 10, 12, 14, 17, 19, 22, 24, 28];
        $actualValues = [];

        foreach ($this->stub as $key => $value) {
            $actualValues[] = $value;
        }

        $this->assertEquals($expectedValues, $actualValues);
    }

    /**
     * Tests if iterator loop is finite.
     */
    public function testValid(): void
    {
        foreach ($this->stub as $value) {
            /* ... */
        }

        // Can be reached only if the loop is finite.
        $this->assertTrue(true);
    }

    /**
     * Tests if the iterator returns valid `foreach` loop keys.
     */
    public function testKey()
    {
        $expectedKeys = [2, 5, 7, 10, 12, 14, 17, 19, 22];
        $actualKeys = [];

        foreach ($this->stub as $key => $value) {
            $actualKeys[] = $key;
        }

        $this->assertEquals($expectedKeys, $actualKeys);
    }

    /**
     * Tests if the iterator can be reused.
     */
    public function testRewind()
    {
        $i = 0;
        foreach ($this->stub as $value) {
            $i++;
        }

        $j = 0;
        foreach ($this->stub as $value) {
            $j++;
        }

        $this->assertEquals($i, $j);
    }

    /**
     * Data provider for {@see TextIteratorTest::testPrevious()}.
     *
     * @return array args
     */
    public function prevProvider(): array
    {
        return [
            [1, 2],
            [0, 0]
        ];
    }

    /**
     * Data provider for {@see TextIteratorTest::testFollowing()}.
     *
     * @return array args
     */
    public function followingProvider(): array
    {
        return [
            [1, 14],
            [7, 28]
        ];
    }
}
