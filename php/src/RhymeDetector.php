<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet;

/**
 * Detects rhymes.
 *
 * @package SpotAlfa\Rifmoplet
 */
class RhymeDetector
{
    /** @var int flag for non-strict comparison of *E* and *I* */
    public const WEAK_E = 1;
    /** @var int flag for non-strict comparison of *O* and *A* */
    public const WEAK_O = 2;
    /** @var int flag used to allow accent shift */
    public const ACCENT_SHIFT = 4;

    /** @var int flags bitmask */
    private $settings = 0;

    /** @var TextIterator first substring container */
    private $first;
    /** @var TextIterator second substring container */
    private $second;

    /**
     * RhymeDetector constructor.
     *
     * @param TextIterator $first {@see RhymeDetector::$first}
     * @param TextIterator $second {@see RhymeDetector::$second}
     */
    public function __construct(TextIterator $first, TextIterator $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * Configures detector options.
     *
     * @param int $settings flags to use
     */
    public function configure(int $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * Main method.
     *
     * Tests if {@see RhymeDetector::$first} matches {@see RhymeDetector::$second} as a rhyme.
     *
     * @return bool the result of the checkout
     */
    public function isRhyme(): bool
    {
        if (!$this->hasAttr(self::ACCENT_SHIFT) && $this->hasAccentShift()) {
            return false;
        } else {
            $first = $this->filterVowels($this->first->slice());
            $second = $this->filterVowels($this->second->slice());

            $this->mergeStressed($first, $second);

            return $this->equals($first, $second);
        }
    }

    /**
     * Checks if there are possible accent shift between {@see RhymeDetector::$first} and {@see RhymeDetector::$second}.
     *
     * @return bool the result of the checkout
     */
    private function hasAccentShift(): bool
    {
        $vowel = function (string $char): bool {
            return (bool)preg_match('/[aeiouwy]/i', $char);
        };

        $next = $this->first->following($vowel);
        $prev = $this->first->previous($vowel);

        $firstStart = $this->isUpper($this->first->slice()[0]);
        $firstEnd = $this->isUpper($this->first->slice()[-1]);
        $firstNext = $next !== false ? $this->isUpper($this->first->get()[$next + $this->first->current()]) : false;
        $firstPrev = $prev !== false ? $this->isUpper($this->first->get()[$this->first->key() - $prev]) : false;

        $next = $this->second->following($vowel);
        $prev = $this->second->previous($vowel);

        $secondStart = $this->isUpper($this->second->slice()[0]);
        $secondEnd = $this->isUpper($this->second->slice()[-1]);
        $secondNext = $next !== false ? $this->isUpper($this->second->get()[$next + $this->second->current()]) : false;
        $secondPrev = $prev !== false ? $this->isUpper($this->second->get()[$this->second->key() - $prev]) : false;

        $start = (!$firstStart && $secondPrev) || (!$secondStart && $firstPrev);
        $end = (!$firstEnd && $secondNext) || (!$secondEnd && $firstNext);

        return $start || $end;
    }

    /**
     * Merges stressed syllables.
     *
     * _aOOu_, _eiow_ -> _aOOu_, _eIOw
     *
     * @param string $first first string
     * @param string $second second string
     */
    private function mergeStressed(string &$first, string &$second): void
    {
        $firstStressed = '';
        $secondStressed = '';
        for ($i = 0, $len = strlen($first); $i < $len; $i++) {
            $next = $i != 0 && ($this->isUpper($first[$i - 1]) xor $this->isUpper($second[$i - 1]));
            $prev = $i != $len - 1 && ($this->isUpper($first[$i + 1]) xor $this->isUpper($second[$i + 1]));
            if ($next || $prev) {
                continue;
            } elseif ($this->isUpper($first[$i]) && $this->isUpper($second[$i])) {
                $firstStressed .= $first[$i];
                $secondStressed .= $second[$i];
            }
        }
        $first = strtolower($firstStressed);
        $second = strtolower($secondStressed);
    }

    /**
     * Keeps only vowels in string.
     *
     * @param string $subj string to filter
     *
     * @return string filtered string
     */
    private function filterVowels(string $subj): string
    {
        $xres = implode(
            $res = array_filter(
                str_split($subj),
                function (string $char): bool {
                    return (bool)preg_match('/[aoieuwy]/i', $char);
                }
            )
        );
        return $xres;
    }

    /**
     * Checks if the given character is upper.
     *
     * @param string $char character to check
     *
     * @return bool the result of the checkout
     */
    private function isUpper(string $char): bool
    {
        return $char == strtoupper($char);
    }

    /**
     * Another complicated string comparison method.
     *
     * @param string $first first string
     * @param string $second second string
     *
     * @return bool true if given strings are equal
     */
    private function equals(string $first, string $second): bool
    {
        if ($first == '' && $second == '') {
            return false;
        }

        $y = $this->hasAttr(self::WEAK_E);
        $w = $this->hasAttr(self::WEAK_O);

        $equals = true;
        for ($i = 0, $len = strlen($first); $i < $len; $i++) {
            $pair = [$first[$i], $second[$i]];

            $eq = $first[$i] == $second[$i];
            $ei = in_array('y', $pair) && (in_array('i', $pair) || (in_array('e', $pair) && $y));
            $oa = in_array('w', $pair) && (in_array('a', $pair) || (in_array('o', $pair) && $w));

            if (!($eq || $ei || $oa)) {
                $equals = false;
                break;
            }
        }

        return $equals;
    }

    /**
     * Checks if the given flag is set on in {@see RhymeDetector::$settings}.
     *
     * @param int $attr an attribute
     *
     * @return bool the result of the checkout
     */
    private function hasAttr(int $attr): bool
    {
        return ($this->settings & $attr) != 0;
    }
}