<?php

namespace Differ\Formatters\Pretty;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isNested;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getType;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getOldValue;
use function Differ\Formatters\Common\getChildren;
use function Differ\Formatters\Common\checkForBool;

function renderPretty(array $array, $tab = "")
{
    if ($tab === "") {
        $result[] = '{';
    }
    
    $result[] = array_reduce($array, function ($acc, $node) use ($tab) {
        $key = getKey($node);
        $type = getType($node);
        switch ($type) {
            case 'changed':
                $oldValue = checkForBool(getOldValue($node));
                $newValue = checkForBool(getNewValue($node));
                $acc[] = "{$tab}  + {$key}: {$newValue}";
                $acc[] = "{$tab}  - {$key}: {$oldValue}";
                break;
            case 'nested':
                $acc[] = "{$tab}    {$key}: {";
                $acc[] = renderPretty(getChildren($node), $tab . "    ");
                $acc[] = "{$tab}    }";
                break;
            // Folloing piece of code was refactored to remove duplicated code
            // and unify code for different types.
            case 'same':
            case 'removed':
            case 'added':
                $sign = getSign($type);
                $nodeValue = getTypeBasedValue($node, $type);
                if (is_object($nodeValue)) {
                    $acc[] = "{$tab}  {$sign} {$key}: {";
                    $acc[] = renderObject($nodeValue, $tab);
                    $acc[] = "{$tab}    }";
                } else {
                    $checedValue = checkForBool(getTypeBasedValue($node, $type));
                    $acc[] = "{$tab}  {$sign} {$key}: {$checkedValue}";
                }
                break;
        }
        return $acc;
    }, []);
    
    if ($tab === "") {
        $result[] = "}";
        $result[] = '';
        return implode("\n", flattenAll($result));
    }
    
    return flattenAll($result);
}

function getTypeBasedValue($node, $type)
{
    switch ($type) {
        case 'same':
        case 'added':
            $result = getNewValue($node);
            break;
        case 'removed':
            $result = getOldValue($node);
            break;
    }
    return $result;
}

function getSign($type)
{
    switch ($type) {
        case 'same':
            $sign = ' ';
            break;
        case 'added':
            $sign = '+';
            break;
        case 'removed':
            $sign = '-';
            break;
    }
    return $sign;
}

function renderObject($object, $tab)
{
    $result = [];
    $objectVars = get_object_vars($object);
    foreach ($objectVars as $key => $value) {
        if (is_object($value)) {
        } else {
            $checkedForBool = checkForBool($value);
            $result[] = "{$tab}        {$key}: {$checkedForBool}";
        }
    }
    
    return $result;
}
