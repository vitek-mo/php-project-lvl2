<?php

namespace Differ\Analyzer;

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Differ\FileDriver\getFilesContent;
use function Differ\FileDriver\getFileExtension;
use function Differ\Formatters\Pretty\renderPretty;
use function Differ\Formatters\Plain\renderPlain;
use function Differ\Formatters\Json\renderJson;

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
    } elseif ($format === 'plain') {
        $rendered = renderPlain($dif);
    } else {
        $rendered = renderPretty($dif);
    }
    return $rendered;
}

function makeNode($type, $key, $oldValue, $newValue, $children)
{
    return [
        'type' => $type,
        'key' => $key,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
        'children' => $children
    ];
}

function makeAst($array1, $array2)
{
    $keys = union(array_keys($array1), array_keys($array2));
    $result = array_reduce($keys, function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array1)) {
            $acc[] = makeNode('added', $key, null, $array2[$key], null);
            return $acc;
        }
        if (!array_key_exists($key, $array2)) {
            $acc[] = makeNode('removed', $key, $array1[$key], null, null);
            return $acc;
        }
        if ($array1[$key] === $array2[$key]) {
            $acc[] = makeNode('same', $key, $array1[$key], $array2[$key], null);
            return $acc;
        }
        
        if (is_object($array1[$key]) && is_object($array2[$key])) {
            if ($array1[$key] != $array2[$key]) {
                $deeperData1 = get_object_vars($array1[$key]);
                $deeperData2 = get_object_vars($array2[$key]);
                $acc[] = makeNode('children', $key, null, null, makeAst($deeperData1, $deeperData2));
            } else {
                $deeperData = get_object_vars($array1[$key]);
                $acc[] = makeNode('same', $key, $deeperData, $deeperData, null);
            }
            return $acc;
        }
        if ($array1[$key] !== $array2[$key]) {
            $acc[] = makeNode('changed', $key, $array1[$key], $array2[$key], null);
            return $acc;
        }
    }, []);
    return $result;
}
