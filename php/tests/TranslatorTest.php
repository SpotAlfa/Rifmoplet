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
        $translator = new Translator($input);
        $expectedOut = 'mytfEt\', fOtka, palalAjka, hApka-uhAnka';
        $inCharset = preg_split('//u', 'АабвгдЕеЁёжзИийклмнОопрстУуфхцчшщъЫыьЭэЮюЯя', -1, PREG_SPLIT_NO_EMPTY);
        $outCharset = str_split('AapfktEyOohsIijklmnOwprstUufksqhq"Ii\'EyUuAi');

        $actualOut = $translator->replace($inCharset, $outCharset);

        $this->assertEquals($expectedOut, $actualOut);
    }
}
