<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfax@gmail.com>
 * @license MIT
 */

namespace SpotAlfa\Rifmoplet;

use Psr\Log\AbstractLogger;

/**
 * Simple logger.
 *
 * @package SpotAlfa\Rifmoplet
 */
class Logger extends AbstractLogger
{
    /**
     * Produces some output.
     *
     * @param mixed $level log level
     * @param string $message text to log
     * @param array $context replace pairs
     */
    public function log($level, $message, array $context = []): void
    {
        echo '[' . strtoupper($level) . ']: ' . $this->interpolate($message, $context) . PHP_EOL;
    }

    /**
     * Interpolates given string.
     *
     * @param string $message subject to interpolate
     * @param array $context replace pairs
     *
     * @return string processed string
     */
    private function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
                $replace['{' . $key . '}'] = $value;
            }
        }

        return strtr($message, $replace);
    }
}