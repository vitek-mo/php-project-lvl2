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

function makeDif($valueBefore, $valueAfter)
{
    $keys = union(array_keys($valueBefore), array_keys($valueAfter));
    $result = array_reduce($keys, function ($acc, $key) use ($valueBefore, $valueAfter) {
        if (!array_key_exists($key, $valueBefore)) {
            $acc[] = makeNode('added', $key, null, $valueAfter[$key], null);
            return $acc;
        }
        if (!array_key_exists($key, $valueAfter)) {
            $acc[] = makeNode('removed', $key, $valueBefore[$key], null, null);
            return $acc;
        }
        if ($valueBefore[$key] === $valueAfter[$key]) {
            $acc[] = makeNode('same', $key, $valueBefore[$key], $valueAfter[$key], null);
            return $acc;
        }
        
        if (is_object($valueBefore[$key]) && is_object($valueAfter[$key])) {
            if ($valueBefore[$key] != $valueAfter[$key]) {
                $deeperData1 = get_object_vars($valueBefore[$key]);
                $deeperData2 = get_object_vars($valueAfter[$key]);
                $acc[] = makeNode('nested', $key, null, null, makeDif($deeperData1, $deeperData2));
            } else {
                $deeperData = get_object_vars($valueBefore[$key]);
                $acc[] = makeNode('same', $key, $deeperData, $deeperData, null);
            }
            return $acc;
        }
        if ($valueBefore[$key] !== $valueAfter[$key]) {
            $acc[] = makeNode('changed', $key, $valueBefore[$key], $valueAfter[$key], null);
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
