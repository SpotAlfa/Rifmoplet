<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfax@gmail.com>
 * @license MIT
 */

namespace SpotAlfa\Rifmoplet\Tests;

use SpotAlfa\Rifmoplet\Logger;
use PHPUnit\Framework\TestCase;

/**
 * Tests Logger.
 *
 * @package SpotAlfa\Rifmoplet\Tests
 */
class LoggerTest extends TestCase
{
    /** @var Logger logger instance */
    private $stub;

    /**
     * Creates new stub.
     */
    public function setUp(): void
    {
        $this->stub = new Logger();
    }

    /**
     * Tests if the logger generally works correct.
     */
    public function testLogPlainText(): void
    {
        $expectedOut = '[DEBUG]: Hello, World!' . PHP_EOL;
        $debugMsg = 'Hello, World!';

        $this->expectOutputString($expectedOut);

        $this->stub->debug($debugMsg);
    }

    /**
     * Tests logger string interpolation.
     */
    public function testLogInterpolatedText(): void
    {
        $expectedOut = '[DEBUG]: Hello, World!' . PHP_EOL;
        $debugMsg = '{greetings}, {person}!';
        $msgContext = [
            'greetings' => 'Hello',
            'person' => 'World'
        ];

        $this->expectOutputString($expectedOut);

        $this->stub->debug($debugMsg, $msgContext);
    }
}
