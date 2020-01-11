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
    $rendered = renderer($dif);
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

function renderer(array $array, $tab = "  ")
{
    $result = [];
    if ($tab === "  ") {
        $result[] = "{";
    }
    
    $result[] = array_reduce($array, function ($acc, $node) use ($tab) {
        $sign = getSign($node);
        $digDeep = 0;
        if (isValueIsObject($node)) {
            $end = "{";
        }
        if (isChildren($node)) {
            $end = "{";
            $digDeep = $node['children'];
        }
        if (isValueIsValue($node)) {
            $end = $node['value'];
        }
        $acc[] = "{$tab}{$sign} {$node['key']}: {$end}";
        
        if ($digDeep) {
            $acc[] = renderer($digDeep, $tab . "    ");
        }
        
        if (isValueIsObject($node)) {
            $acc[] = renderObject(get_object_vars($node['value']), $tab);
        }
        
        if (isValueIsObject($node) || $digDeep) {
            $acc[] = "{$tab}  }";
        }
        
        return $acc;
    }, []);
    
    if ($tab === "  ") {
        $result[] = "}";
    }
    $resultFlatten = flattenAll($result);
    $resultString = implode("\n", $resultFlatten);
    return $resultString;
}

function renderObject($node, $tab)
{
    $result = [];
    foreach ($node as $key => $value) {
        $result[] = "{$tab}      {$key}: {$value}";
    }
    return $result;
}

function isSame($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'same') {
            return true;
        }
    }
    return false;
}

function isRemoved($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'removed') {
            return true;
        }
    }
    return false;
}

function isAdded($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'added') {
            return true;
        }
    }
    return false;
}

function getSign($node)
{
    if (isChildren($node) || isSame($node)) {
        return " ";
    } elseif (isRemoved($node)) {
        return "-";
    } elseif (isAdded($node)) {
        return "+";
    } else {
        return "?";
    }
}

function isValueIsValue($node)
{
    if (isset($node['value'])) {
        if ($node['value'] !== null && !is_object($node['value'])) {
            return true;
        }
    }
    return false;
}

function isValueIsObject($node)
{
    if (isset($node['value'])) {
        if (is_object($node['value'])) {
            return true;
        }
    }
    return false;
}

function isNode($node)
{
    if (isset($node['type'])) {
        if (in_array($node['type'], ['same', 'added', 'removed'])) {
            return true;
        }
    }
    return false;
}

function isChildren($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'children') {
            return true;
        }
    }
    return false;
}

function booleazator($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    } else {
        return $value;
    }
}
