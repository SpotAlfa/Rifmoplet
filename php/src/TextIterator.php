<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet;

use Iterator;

/**
 * Iterates through given string.
 *
 * Selects all substrings containing exact syllables.
 *
 * @package SpotAlfa\Rifmoplet
 */
class TextIterator implements Iterator
{
    /** @var string text to iterate through */
    private $text;
    /** @var int text length used in `for` loops */
    private $length;

    /** @var int start index of the selected substring */
    private $start;
    /** @var int end index of the selected substring */
    private $end;
    /** @var int current number of selected syllables */
    private $syllable;

    /** @var int number of syllables to grab */
    private $syllables;

    /**
     * TextIterator constructor.
     *
     * @param string $text {@see TextIterator::$text}
     * @param int $syllables {@see TextIterator::$syllables}
     */
    public function __construct(string $text, int $syllables)
    {
        $this->text = $text;
        $this->length = strlen($text);
        $this->syllables = $syllables;
    }

    /**
     * Returns `foreach` loop value.
     *
     * @return int {@see TextIterator::$end}
     */
    public function current(): int
    {
        return $this->end;
    }

    /**
     * Selects next substring.
     */
    public function next(): void
    {
        $this->end = $this->start + 1;
        for (; $this->valid(); $this->end++) {
            if (preg_match('/[aeiou]/i', $this->text[$this->end])) {
                if ($this->syllable == 0) {
                    $this->start = $this->end;
                }
                $this->syllable++;

                if ($this->syllable == $this->syllables) {
                    $this->syllable = 0;
                    break;
                }
            }
        }
    }

    /**
     * Returns `foreach` loop key.
     *
     * @return int {@see TextIterator::$start}
     */
    public function key(): int
    {
        return $this->start;
    }

    /**
     * Checks if the end of {@see TextIterator::$text} is reached.
     *
     * @return bool the result of the checkout
     */
    public function valid(): bool
    {
        return $this->end < $this->length;
    }

    /**
     * Rewinds iterator state.
     */
    public function rewind(): void
    {
        $this->start = 0;
        $this->end = 0;
        $this->syllable = 0;

        $this->next();
    }
}