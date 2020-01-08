<?php

namespace Differ\Analyzer;

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Funct\Collection\flatten;
use function Funct\Collection\flattenAll;
use function Differ\FileDriver\getFilesContent;

function genDiff($path1, $path2, $format)
{
    $paths = [$path1, $path2];
    try {
        $contents = getFilesContent($paths);
    } catch (\Exception $e) {
        print_r("Error: {$e->getMessage()}\n");
        return;
    }
    
    [$obj1, $obj2] = parse($contents, $format);
    $array1 = get_object_vars($obj1);
    $array2 = get_object_vars($obj2);
    
    $dif = makeAst($array1, $array2);
    return renderer($dif);
}

function makeNode($type, $key, $value)
{
    return [
        'type' => $type,
        'key' => $key,
        'value' => $value
    ];
}

function makeChildren($type, $key, $children)
{
    return [
        'type' => $type,
        'key' => $key,
        'children' => $children
    ];
}

function makeAst($array1, $array2)
{
    $keys = union(array_keys($array1), array_keys($array2));
    asort($keys);
    $result = array_reduce($keys, function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array1)) {
            $acc[] = makeNode('added', $key, booleazator($array2[$key]));
            return $acc;
        }
        if (!array_key_exists($key, $array2)) {
            $acc[] = makeNode('removed', $key, booleazator($array1[$key]));
            return $acc;
        }
        if ($array1[$key] === $array2[$key]) {
            $acc[] = makeNode('same', $key, booleazator($array1[$key]));
            return $acc;
        }
        if (is_object($array1[$key]) && is_object($array2[$key])) {
            $deeperData1 = get_object_vars($array1[$key]);
            $deeperData2 = get_object_vars($array2[$key]);
            $acc[] = makeChildren('children', $key, makeAst($deeperData1, $deeperData2));
            return $acc;
        }
        if ($array1[$key] !== $array2[$key]) {
            $acc[] = makeNode('added', $key, booleazator($array2[$key]));
            $acc[] = makeNode('removed', $key, booleazator($array1[$key]));
            return $acc;
        }
    }, []);
    return $result;
}

function renderer(array $array)
{
    print_r($array);
}

function booleazator($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}

function objToArray($value)
{
    if (is_object($value)) {
        return "";
    } else {
        return $value;
    }
}