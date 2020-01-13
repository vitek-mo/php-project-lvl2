<?php

namespace Differ\Formatters\Json;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;

function renderJson(array $array, $tab = "  ")
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
            $acc[] = renderJson($digDeep, $tab . "    ");
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
        if (!is_object($value)) {
            $result[] = "{$tab}      {$key}: {$value}";
        } else {
            $result[] = "{$tab}      {$key}: {";
            $result[] = renderObject($value, $tab . "    ");
            $result[] = "{$tab}      }";
        }
    }
    return $result;
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

function isValueIsObject($node)
{
    if (isset($node['value'])) {
        if (is_object($node['value'])) {
            return true;
        }
    }
    return false;
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

function isNode($node)
{
    if (isset($node['type'])) {
        if (in_array($node['type'], ['same', 'added', 'removed'])) {
            return true;
        }
    }
    return false;
}