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
    /** @var string processed text */
    private $subject;

    /**
     * Translator constructor.
     *
     * @param string $subject {@see Translator::$subject}
     */
    public function __construct(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Replaces characters in {@see Translator::$subject}.
     *
     * @param array $in input charset
     * @param array $out output charset
     *
     * @return string processed string
     */
    public function replace(array $in, array $out): string
    {
        $replacePairs = array_combine($in, $out);

        return strtr($this->subject, $replacePairs);
    }
}