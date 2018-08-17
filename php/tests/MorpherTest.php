<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet\Tests;

use SpotAlfa\Rifmoplet\InvalidArgumentException;
use SpotAlfa\Rifmoplet\Morpher;
use PHPUnit\Framework\TestCase;

/**
 * Tests Morpher class.
 *
 * @package SpotAlfa\Rifmoplet\Tests
 */
class MorpherTest extends TestCase
{
    /** @var string path to the existing binary morpher */
    private const EXISTING_BIN = __DIR__ . '/../../cs/Rifmoplet/bin/Release/Rifmoplet.exe';
    /** @var string path to non-existing file */
    private const NOT_EXISTING_BIN = __DIR__ . '/binary.exe';

    /**
     * Tests if the morpher can be created from the valid path to binary.
     *
     * @throws InvalidArgumentException expected not to be thrown
     */
    public function testValidBin(): void
    {
        new Morpher(self::EXISTING_BIN);

        $this->assertTrue(true);
    }

    /**
     * Tests if an exception is thrown when non-existing binary is passed to the morpher.
     *
     * @throws InvalidArgumentException because binary given to the morpher actually does not exist
     */
    public function testInvalidBin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Morpher(self::NOT_EXISTING_BIN);
    }

    /**
     * Tests if the morpher can obtain correct accents of the given words.
     *
     * @depends testInvalidBin
     *
     * @throws InvalidArgumentException expected not to be thrown
     */
    public function testGetAccents(): void
    {
        $morph = new Morpher(self::EXISTING_BIN);
        $words = ['всё', 'переплетено', 'телик', 'и', 'террор'];
        $expectedAccents = [2, 10, 255, 0, 4];

        $actualAccents = $morph->getAccents(...$words);

        $this->assertEquals($expectedAccents, $actualAccents);
    }
}
