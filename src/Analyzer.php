<?php

namespace Differ\Analyzer;

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Differ\FileDriver\getFilesContent;
use function Differ\FileDriver\getFileExtension;
use function Differ\Formatters\Json\renderJson;
use function Differ\Formatters\Plain\renderPlain;

function genDiff($path1, $path2, $format)
{
    $paths = [$path1, $path2];
    try {
        $contents = getFilesContent($paths);
    } catch (\Exception $e) {
        print_r("Error: {$e->getMessage()}\n");
        return;
    }
    
    $outputFormat1 = getFileExtension($path1);
    $outputFormat2 = getFileExtension($path2);
    $outputFormats = [$outputFormat1, $outputFormat2];
    
    [$obj1, $obj2] = parse($contents, $outputFormats);
    $array1 = get_object_vars($obj1);
    $array2 = get_object_vars($obj2);
    
    $dif = makeAst($array1, $array2);
    if ($format === 'json') {
        $rendered = renderJson($dif);
    }
    if ($format === 'plain') {
        $rendered = renderPlain($dif);
    }
    return $rendered;
}

function makeNode($type, $key, $value, $children)
{
    return [
        'type' => $type,
        'key' => $key,
        'value' => $value,
        'children' => $children
    ];
}

function makeAst($array1, $array2)
{
    $keys = union(array_keys($array1), array_keys($array2));
    $result = array_reduce($keys, function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array1)) {
            $acc[] = makeNode('added', $key, booleazator($array2[$key]), null);
            return $acc;
        }
        if (!array_key_exists($key, $array2)) {
            $acc[] = makeNode('removed', $key, booleazator($array1[$key]), null);
            return $acc;
        }
        if ($array1[$key] === $array2[$key]) {
            $acc[] = makeNode('same', $key, booleazator($array1[$key]), null);
            return $acc;
        }
        if (is_object($array1[$key]) && is_object($array2[$key])) {
            $deeperData1 = get_object_vars($array1[$key]);
            $deeperData2 = get_object_vars($array2[$key]);
            $acc[] = makeNode('children', $key, null, makeAst($deeperData1, $deeperData2));
            return $acc;
        }
        if ($array1[$key] !== $array2[$key]) {
            $acc[] = makeNode('added', $key, booleazator($array2[$key]), null);
            $acc[] = makeNode('removed', $key, booleazator($array1[$key]), null);
            return $acc;
        }
    }, []);
    return $result;
}

function booleazator($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}
