<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfax@gmail.com>
 * @license MIT
 */


namespace SpotAlfa\Rifmoplet\Tests;

use SpotAlfa\Rifmoplet\Translator;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@see Translator}.
 *
 * @package SpotAlfa\Rifmoplet\Tests
 */
class TranslatorTest extends TestCase
{

    /**
     * Tests if russian words correctly translated into latin charset.
     */
    public function testReplace(): void
    {
        $input = 'медвЕдь, вОдка, балалАйка, шАпка-ушАнка';
        $expectedOut = 'mytfEt\', fOtka, palalAjka, hApka-uhAnka';
        $inCharset = 'АабвгдЕеЁёжзИийклмнОопрстУуфхцчшщъЫыьЭэЮюЯя';
        $outCharset = 'AapfktEyOohsIijklmnOwprstUufksqhq"Ii\'EyUuAi';
        $translator = new Translator($inCharset, $outCharset);

        $actualOut = $translator->replace($input);

        $this->assertEquals($expectedOut, $actualOut);
    }
}
