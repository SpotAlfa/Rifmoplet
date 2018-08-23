<?php
/**
 * This file is a part of the "Rifmoplet" rhyme detector.
 *
 * @author SpotAlfa <spotalfax@gmail.com>
 * @license MIT
 */

namespace SpotAlfa\Rifmoplet;

require __DIR__ . '/../vendor/autoload.php';

function isUpper(string $char): bool
{
    return strtoupper($char) == $char && $char != '~';
}

function array_diff(array $first, array $second): array
{
    $diff = [];
    foreach ($first as $item) {
        if (in_array($item, $second)) {
            continue;
        }
        $diff[] = $item;
    }
    return $diff;
}

function array_unique(array $arr): array
{
    $indexed = [];
    $result = [];
    foreach ($arr as $item) {
        if (!in_array($item, $indexed)) {
            $result[] = $item;
            $indexed[] = $item;
        }
    }
    return $result;
}

$text = file_get_contents(__DIR__ . '/../resources/восточный мордор.txt');
$optionals = explode(PHP_EOL, file_get_contents(__DIR__ . '/../resources/optionals.dict'));
$exceptions = [];
foreach (explode(PHP_EOL, file_get_contents(__DIR__ . '/../resources/exceptions.dict')) as $line) {
    list($key, $value) = explode(' ', $line);
    $exceptions[$key] = $value;
}

$words = preg_split('/[!?@#$%^&*()\[\]\-=_+:;,.\s"\']+/', mb_strtolower($text));
try {
    $morph = new Morpher(__DIR__ . '/../../cs/Rifmoplet/bin/Release/Rifmoplet.exe');
} catch (InvalidArgumentException $err) {
    echo $err->getMessage();
    die($err->getCode());
}
$accents = $morph->getAccents(...$words);

$cyr = file_get_contents(__DIR__ . '/../resources/cyr.charset');
$lat = file_get_contents(__DIR__ . '/../resources/lat.charset');
$translator = new Translator($cyr, $lat);

$transcriptions = [];
for ($i = 0, $maxI = count($words); $i < $maxI; $i++) {
    $word = $words[$i];
    if (isset($exceptions[$word])) {
        $transcriptions[] = $exceptions[$word];
        continue;
    }
    $accent = $accents[$i];

    $word = mb_substr($word, 0, $accent) . mb_strtoupper(mb_substr($word, $accent, 1)) . mb_substr($word, $accent + 1);

    $transcription = $translator->replace($word);
    if (in_array(mb_strtolower($word), $optionals)) {
        $transcription = strtolower($transcription);
    }
    $transcriptions[] = $transcription;
}

usort($words, function (string $a, string $b): int {
    return mb_strlen($a) - mb_strlen($b);
});
usort($transcriptions, function (string $a, string $b): int {
    return mb_strlen($a) - mb_strlen($b);
});

$transcription = mb_strtolower($text);
for ($i = count($words) - 1; $i >= 0; $i--) {
    $transcription = str_replace($words[$i], $transcriptions[$i], $transcription);
}

$unit = function (string $char): bool {
    return (bool)preg_match('/[!?@#$%^&*()\[\]\-=_+:;,.\s"\'aoueiwy~]/i', $char);
};

$totalRhymes = [];
for ($i = 12; $i >= 3; $i--) {
    $outer = new TextIterator($transcription, $i);
    $inner = new TextIterator($transcription, $i);
    foreach ($outer as $outerStart => $outerEnd) {
        if (strpos($outer->slice(), PHP_EOL) !== false) {
            continue;
        }
        $outerStressedStart = isUpper($outer->slice()[0]);
        $outerStressedEnd = isUpper($outer->slice()[-1]);
        $rhymes = [];
        if ($outerStressedStart) {
            foreach ($inner as $innerStart => $innerEnd) {
                if ($innerStart == $outerStart && $innerEnd == $outerEnd) {
                    continue;
                }
                $innerStressedEnd = isUpper($inner->slice()[-1]);
                if ($innerStressedEnd || $outerStressedEnd) {
                    $detector = new RhymeDetector($outer, $inner);
                    $rhyme = mb_substr($text, $innerStart, $innerEnd - $innerStart + 1);
                    if ($detector->isRhyme() && strpos($rhyme, PHP_EOL) === false) {
                        $left = $inner->previous($unit);
                        $left = $left === false ? $innerStart : $left - 1;
                        $right = $inner->following($unit);
                        $right = $right === false ? strlen($transcription) - 1 : $right - 1;
                        $rhymes[] = [$innerStart - $left, $innerEnd + $right, $rhyme];
                    }
                }
            }
        }
        if (count($rhymes) != 0) {
            $left = $outer->previous($unit);
            $left = $left === false ? $outerStart : $left - 1;
            $right = $outer->following($unit);
            $right = $right === false ? strlen($transcription) - 1 : $right - 1;
            $rhymes[] = [$outerStart - $left, $outerEnd + $right, mb_substr($text, $outerStart, $outerEnd - $outerStart + 1)];
            foreach ($totalRhymes as $key => $rhymeGroup) {
                $diff1 = count(array_diff($rhymes, $rhymeGroup));
                $diff2 = count(array_diff($rhymeGroup, $rhymes));
                if ($diff1 != 0 && $diff1 < count($rhymes)) { // current array contains already saved
                    $totalRhymes[$key] = array_unique(array_merge($rhymes, $rhymeGroup));
                    continue 2;
                } elseif ($diff2 != 0 && $diff2 < count($rhymeGroup)) { // already saved array contains current
                    continue 2;
                } elseif ($diff1 == 0 && $diff2 == 0) { // these arrays are equal
                    continue 2;
                }
            }
            $totalRhymes[] = $rhymes;
        }
    }
}

foreach ($totalRhymes as &$rhymeGroup) {
    usort($rhymeGroup, function (array $a, array $b): int {
        return $b[0] - $a[0];
    });
}
unset($rhymeGroup);
rsort($totalRhymes);

// TODO: resolve index chains.

foreach ($totalRhymes as $i => $outer) { // фильтрация вложенных рифм
    foreach ($totalRhymes as $j => $inner) {
        if ($i == $j) {
            continue;
        }
        foreach ($outer as $x => $first) {
            foreach ($inner as $y => $second) {
                list($a, $b) = $first;
                list($c, $d) = $second;

                if ($c >= $a && $c <= $b && $d >= $a && $d <= $b) {
                    unset($totalRhymes[$j][$y]);
                }
            }
        }
        if (count($totalRhymes[$j]) == 0) {
            unset($totalRhymes[$j]);
        } else {
            $totalRhymes[$j] = $inner;
        }
    }
}

localRhymes:
foreach ($totalRhymes as $x => $rhymeGroup) { // разрешение пересечений внутри каждой группы
    foreach ($rhymeGroup as $i => $outer) {
        foreach ($rhymeGroup as $j => $inner) {
            if ($i == $j) {
                continue;
            }

            list($a, $b) = $outer;
            list($c, $d) = $inner;

            if ($a > $c && $a <= $d && $b > $c && $b > $d) {
                unset($totalRhymes[$x][$j]);
                if (count($totalRhymes[$x]) <= 1) {
                    unset($totalRhymes[$x]);
                }
                goto localRhymes;
            }
        }
    }

    if (count($totalRhymes[$x]) <= 1) {
        unset($totalRhymes[$x]);
        goto localRhymes;
    }
}

globalRhymes:
foreach ($totalRhymes as $i => $outer) { // разрешение пересечений между разными группами
    foreach ($totalRhymes as $j => $inner) {
        if ($i == $j) {
            continue;
        }
        foreach ($outer as $x => $first) {
            foreach ($inner as $y => $second) {
                list($a, $b) = $first;
                list($c, $d) = $second;

                if ($a > $c && $a <= $d && $b > $c && $b > $d) {
                    unset($totalRhymes[$j][$y]);
                    if (count($totalRhymes[$j]) <= 1) {
                        unset($totalRhymes[$j]);
                    }
                    goto globalRhymes;
                }
            }
        }

        if (count($totalRhymes[$j]) <= 1) {
            unset($totalRhymes[$j]);
            goto globalRhymes;
        }
    }
}

foreach ($totalRhymes as $rhymeGroup) {
    foreach ($rhymeGroup as $rhyme) {
        list($start, $end) = $rhyme;
        echo mb_substr($text, $start, $end - $start + 1) . PHP_EOL;
    }
    echo PHP_EOL;
}

$html = '';

usort($totalRhymes, function (array $a, array  $b): int {
    $a = preg_match_all('/[аеёиоуыэюя~]/iu', $a[0][2]);
    $b = preg_match_all('/[аеёиоуыэюя~]/iu', $b[0][2]);

    return $b - $a;
});

$class = 'a';
$indexes = [];
foreach ($totalRhymes as $rhymeGroup) {
    foreach ($rhymeGroup as $rhyme) {
        list($start, $end) = $rhyme;
        @$indexes[$start] .= "<div class='rhyme {$class}'>";
        @$indexes[$end + 1] .= "</div>";
    }
    $class++;
}

ksort($indexes);

//print_r($indexes);

$prev = 0;
foreach ($indexes as $index => $tag) {
    $html .= mb_substr($text, $prev, $index - $prev) . $tag;
    $prev = $index;
}
$html .= mb_substr($text, $prev);
$html = explode(PHP_EOL, $html);
array_walk($html, function (string &$line): void {
    $line = "<div class='space'>{$line}</div><br>" . PHP_EOL;
});
$html = implode($html);
$html = str_replace('~', '', $html);
$html = "<div class='outer'><div class='inner'>{$html}</div></div>";

/*preg_match_all('/[^!?@#$%^&*()\[\]\-=_+:;,.\s]+(?=\r\n)/', $text, $words);
preg_match_all('/[^!?@#$%^&*()\[\]\-=_+:;,.\s]+(?=\r\n)/', $transcription, $transcriptions);

$words = $words[0];
$transcriptions = $transcriptions[0];

foreach ($transcriptions as &$transcription) {
    $transcription = implode(
        array_filter(
            str_split($transcription),
            function (string $char): bool {
                return (bool)preg_match('/[aoieuwy]/i', $char);
            }
        )
    );
}

$class = 'a';
$indexed = [];
foreach ($transcriptions as $i => $outer) {
    foreach ($transcriptions as $j => $inner) {
        if ($i == $j || in_array($j, $indexed) || in_array($i, $indexed)) {
            continue;
        }

        for ($x = 1, $len = min(strlen($outer), strlen($inner)); $x <= $len; $x++) {
            if ($outer[-$x] == $inner[-$x] && isUpper($outer[-$x])) {
                $html = str_replace($words[$i], "<div class='rhyme {$class}'>{$words[$i]}</div>", $html);
                $html = str_replace($words[$j], "<div class='rhyme {$class}'>{$words[$j]}</div>", $html);
                $class++;
                $indexed[] = $j;
                $indexed[] = $i;
                break;
            }
        }
    }
}
*/

echo $html;
