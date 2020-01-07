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
    return analyze($contents, $format);
}

//Accepts array of two elements in JSON format. Return difference.
function analyze($contents, $format)
{
    [$obj1, $obj2] = parse($contents, $format);
    
    $array1 = get_object_vars($obj1);
    $array2 = get_object_vars($obj2);
    
    $keys = union(array_keys($array1), array_keys($array2));
    asort($keys);
    
    $result = flatten(flatten(array_map(function ($key) use ($array1, $array2) {
        $item = [];
        if (!array_key_exists($key, $array1)) {
            $item[] = [['type' => 'added', 'key' => $key, 'value' => $array2[$key], 'children' => null]];
        } elseif (!array_key_exists($key, $array2)) {
            $item[] = [['type' => 'removed', 'key' => $key, 'value' => $array1[$key], 'children' => null]];
        } elseif ($array1[$key] === $array2[$key]) {
            $item[] = [['type' => 'same', 'key' => $key, 'value' => $array1[$key], 'children' => null]];
        } else {
            $item[] = [
                    ['type' => 'removed', 'key' => $key, 'value' => $array1[$key], 'children' => null],
                    ['type' => 'added', 'key' => $key, 'value' => $array2[$key], 'children' => null]
                    ];
        }
        return $item;
    }, $keys)));
    return renderer($result);
}

function renderer(array $array, $tab = "")
{
    $keys = array_keys($array);
    $result[] = "{$tab}{";
    $result[] = array_reduce($keys, function ($acc, $key) use ($array, $tab) {
        $element = $array[$key];
        if (is_array($element) && isset($element['type'])) {
            if ($element['type'] !== 'children') {
                switch ($element['type']) {
                    case 'same':
                        $sign = ' ';
                        break;
                    case 'added':
                        $sign = '+';
                        break;
                    case 'removed':
                        $sign = '-';
                        break;
                    default:
                        $sign = '?';
                }
                if (is_bool($element['value'])) {
                    $value = $element['value'] ? 'true' : 'false';
                } else {
                    $value = $element['value'];
                }
                $acc[] = "{$tab}  {$sign} {$element['key']}: {$value}";
            }
        }
        return $acc;
    }, []);
    $result[] = "}";
    return implode("\n", flattenAll($result));
}
