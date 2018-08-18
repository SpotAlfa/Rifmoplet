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

        $this->rewind();
    }

    /**
     * Gets substring from {@see TextIterator::$text} using {@see TextIterator::$start} and {@see TextIterator::$end} as
     * start and end indexes.
     *
     * @param int $left adjust start index
     * @param int $right adjust end index
     *
     * @return string current substring
     */
    public function slice(int $left = 0, int $right = 0): string
    {
        $right += $left;
        return substr($this->text, $this->start - $left, $this->end - $this->start + $right + 1);
    }

    /**
     * Getter for {@see TextIterator::$text}.
     *
     * @return string {@see TextIterator::$text}
     */
    public function get(): string
    {
        return $this->text;
    }

    /**
     * Obtains distance to the first character before {@see TextIterator::$start} matching callback.
     *
     * @param callable $match callback-matcher
     *
     * @return int|false distance to the first match or false if nothing matches
     */
    public function previous(callable $match)
    {
        for ($x = 1; $x <= $this->start; $x++) {
            if ($match($this->text[$this->start - $x])) {
                return $x;
            }
        }
        return false;
    }

    /**
     * Obtains distance to the first character after {@see TextIterator::$end} matching callback.
     *
     * @param callable $match callback-matcher
     *
     * @return int|false distance to the first match or false if nothing matches
     */
    public function following(callable $match)
    {
        for ($x = 1, $len = $this->length - $this->end - 1; $x <= $len; $x++) {
            if ($match($this->text[$this->end + $x])) {
                return $x;
            }
        }
        return false;
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
            if (preg_match('/[aeiouwy]/i', $this->text[$this->end])) {
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
        $this->start = -1;
        $this->end = 0;
        $this->syllable = 0;

        $this->next();
    }
}