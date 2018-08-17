<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet;

/**
 * Performs morphological analysis of words to get their accents.
 *
 * Actually, this class is a wrapper for `.exe` utility called using given path.
 *
 * @package SpotAlfa\Rifmoplet
 */
class Morpher
{
    /** @var string stores path to a binary executable */
    private $binary;

    /**
     * Morpher constructor.
     *
     * @param string $binary path to real morpher
     *
     * @throws InvalidArgumentException if given binary does not exist
     */
    public function __construct(string $binary)
    {
        if (!$this->binaryExists($binary)) {
            throw new InvalidArgumentException('cannot find given binary');
        }

        $this->binary = $binary;
    }

    /**
     * Obtains accents of given words.
     *
     * @param string ...$words words to process
     *
     * @return int[] stressed symbols
     */
    public function getAccents(string ...$words): array
    {
        $accents = explode("\n", $this->callBinary(...$words));
        array_pop($accents);

        return $accents;
    }

    /**
     * Executes binary.
     *
     * @param string ...$args arguments passed to morpher utility
     *
     * @return string morpher output
     */
    private function callBinary(string ...$args): string
    {
        $cmd = $this->binary . ' '. implode(' ', $args);
        $out = `$cmd`;

        return $out;
    }

    /**
     * Checks if the given file exists.
     *
     * @param string $filename path to the file
     *
     * @return bool the result of the checkout
     */
    private function binaryExists(string $filename): bool
    {
        return file_exists($filename) && is_file($filename);
    }
}