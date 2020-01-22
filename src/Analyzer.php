<?php

namespace Differ\Analyzer;

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Differ\Formatters\Pretty\renderPretty;
use function Differ\Formatters\Plain\renderPlain;
use function Differ\Formatters\Json\renderJson;

function genDiff($path1, $path2, $format)
{
    
    $content1 = getFileContent($path1);
    $content2 = getFileContent($path2);
    
    $outputFormat1 = pathinfo($path1, $options = PATHINFO_EXTENSION);
    $outputFormat2 = pathinfo($path2, $options = PATHINFO_EXTENSION);
    
    $obj1 = parse($content1, $outputFormat1);
    $obj2 = parse($content2, $outputFormat2);
    
    $array1 = get_object_vars($obj1);
    $array2 = get_object_vars($obj2);
    
    $dif = makeDif($array1, $array2);
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

function makeDif($array1, $array2)
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
                $acc[] = makeNode('children', $key, null, null, makeDif($deeperData1, $deeperData2));
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

function getFileContent($path)
{
    if (!is_readable($path)) {
        throw new \Exception("'{$path}' is not exist or not readable.");
        return null;
    }
    return file_get_contents($path);
}
