<?php

namespace Differ\Analyzer;

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Differ\Formatters\Pretty\renderPretty;
use function Differ\Formatters\Plain\renderPlain;
use function Differ\Formatters\Json\renderJson;

function genDiff($inputFilePath1, $inputFilePath2, $outputFormat)
{
    
    $rawContent1 = getFileContent($inputFilePath1);
    $rawContent2 = getFileContent($inputFilePath2);
    
    $fileExtension1 = pathinfo($inputFilePath1, $options = PATHINFO_EXTENSION);
    $fileExtension2 = pathinfo($inputFilePath2, $options = PATHINFO_EXTENSION);
    
    $object1 = parse($rawContent1, $fileExtension1);
    $object2 = parse($rawContent2, $fileExtension2);
    $object1Vars = get_object_vars($object1);
    $object2Vars = get_object_vars($object2);
    
    $dif = makeDif($object1Vars, $object2Vars);
    if ($outputFormat === 'json') {
        $rendered = renderJson($dif);
    } elseif ($outputFormat === 'plain') {
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
    }
    return file_get_contents($path);
}
