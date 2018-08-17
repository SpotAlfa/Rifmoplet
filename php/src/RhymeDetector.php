<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfa@gmail.com>
 * @license  MIT
 */

namespace SpotAlfa\Rifmoplet;


class RhymeDetector
{
    public const WEAK_E = 1;
    public const WEAK_O = 2;
    public const ACCENT_SHIFT = 4;

    private $settings = 0;

    private $first;
    private $second;

    public function __construct(TextIterator $first, TextIterator $second)
    {
        $this->first = $first;
        $this->second = $second;
    }

    public function configure(int $settings): void
    {
        $this->settings = $settings;
    }

    public function isRhyme(): bool
    {
        if (!$this->hasAttr(self::ACCENT_SHIFT) && $this->hasAccentShift()) {
            return false;
        } else {
            $first = $this->first->slice();
            $second = $this->second->slice();

            $this->mergeStressed($first, $second);

            return $this->equals($first, $second);
        }
    }

    private function hasAccentShift(): bool
    {
        $vowel = function (string $char): bool {
            return (bool)preg_match('/aeiouwy/i', $char);
        };

        $firstStart = $this->isUpper($this->first[0]);
        $firstEnd = $this->isUpper($this->first[-1]);
        $firstNext = $this->first->get()[$this->first->following($vowel) + $this->first->current()];
        $firstPrev = $this->first->get()[$this->first->previous($vowel) + $this->first->key()];

        $secondStart = $this->isUpper($this->second[0]);
        $secondEnd = $this->isUpper($this->second[-1]);
        $secondNext = $this->second->get()[$this->second->following($vowel) + $this->second->current()];
        $secondPrev = $this->second->get()[$this->second->previous($vowel) + $this->second->key()];

        $start = (!$firstStart && $secondPrev) || (!$secondStart && $firstPrev);
        $end = (!$firstEnd && $secondNext) || (!$secondEnd && $firstNext);

        return $start || $end;
    }

    private function mergeStressed(string &$first, string &$second): void
    {
        $firstStressed = '';
        $secondStressed = '';
        for ($i = 0, $len = strlen($first); $i < $len; $i++) {
            $next = $i != 0 && ($this->isUpper($first[$i - 1]) || $this->isUpper($first[$i - 1]));
            $prev = $i != $len - 1 && ($this->isUpper($first[$i + 1]) || $this->isUpper($first[$i + 1]));
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

    private function isUpper(string $char): bool
    {
        return $char == strtoupper($char);
    }

    private function equals(string $first, string $second): bool
    {
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

    private function hasAttr(int $attr): bool
    {
        return $this->settings & $attr != 0;
    }
}