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
}
