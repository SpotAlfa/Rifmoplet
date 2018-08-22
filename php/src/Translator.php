<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfax@gmail.com>
 * @license MIT
 */

namespace SpotAlfa\Rifmoplet;

/**
 * Translates one charset to another.
 *
 * @package SpotAlfa\Rifmoplet
 */
class Translator
{
    /** @var string[] key-value replace pairs */
    private $replacePairs;

    /**
     * Translator constructor.
     *
     * @param string $in input charset
     * @param string $out output charset
     */
    public function __construct(string $in, string $out)
    {
        $in = preg_split('//u', $in, -1, PREG_SPLIT_NO_EMPTY);
        $out = preg_split('//u', $out, -1, PREG_SPLIT_NO_EMPTY);

        $this->replacePairs = array_combine($in, $out);
    }

    /**
     * Replaces characters in {@see Translator::$subject}.
     *
     * @param string $subject string to process
     *
     * @return string processed string
     */
    public function replace(string $subject): string
    {
        return strtr($subject, $this->replacePairs);
    }
}