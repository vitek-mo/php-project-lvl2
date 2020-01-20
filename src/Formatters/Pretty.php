<?php

namespace Differ\Formatters\Pretty;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getChildren;
use function Differ\Formatters\Common\checkForBool;

function renderPretty(array $array, $tab = "")
{
    $result = [];
    if ($tab === "") {
        $result[] = '{';
    }
    
    $result[] = array_reduce($array, function ($acc, $node) use ($tab) {
        $key = getKey($node);
        if (isChanged($node)) {
            if (is_object(getOldValue($node))) {
            } else {
                $oldValue = checkForBool(getOldValue($node));
            }
            if (is_object(getNewValue($node))) {
            } else {
                $newValue = checkForBool(getNewValue($node));
            }
            $acc[] = "{$tab}  + {$key}: {$newValue}";
            $acc[] = "{$tab}  - {$key}: {$oldValue}";
        }
        
        if (isChildren($node)) {
            $acc[] = "{$tab}    {$key}: {";
            $acc[] = renderPretty(getChildren($node), $tab . "    ");
            $acc[] = "{$tab}    }";
        }
        
        if (isSame($node)) {
            if (is_object(getNewValue($node))) {
                $acc[] = "{$tab}    {$key}: {";
                $acc[] = renderObject(getNewValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $value = checkForBool(getNewValue($node));
                $acc[] = "{$tab}    {$key}: {$value}";
            }
        }
        
        if (isRemoved($node)) {
            if (is_object(getOldValue($node))) {
                $acc[] = "{$tab}  - {$key}: {";
                $acc[] = renderObject(getOldValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $oldValue = checkForBool(getOldValue($node));
                $acc[] = "{$tab}  - {$key}: {$oldValue}";
            }
        }
        
        if (isAdded($node)) {
            if (is_object(getNewValue($node))) {
                $acc[] = "{$tab}  + {$key}: {";
                $acc[] = renderObject(getNewValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $newValue = checkForBool(getNewValue($node));
                $acc[] = "{$tab}  + {$key}: {$newValue}";
            }
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

function renderObject($object, $tab)
{
    $result = [];
    $array = get_object_vars($object);
    foreach ($array as $key => $value) {
        if (is_object($value)) {
        } else {
            $checkedForBool = checkForBool($value);
            $result[] = "{$tab}        {$key}: {$checkedForBool}";
        }
    }
    
    return $result;
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

function isSame($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'same') {
            return true;
        }
    }
    return false;
}

function isChanged($node)
{
    if (isset($node['type'])) {
        if ($node['type'] === 'changed') {
            return true;
        }
    }
    return false;
}

function isSigned($node)
{
    if (isChildren($node) || isSame($node)) {
        return false;
    }
    return true;
}

function getOldValue($node)
{
    if (isset($node['oldValue'])) {
        return $node['oldValue'];
    }
    return null;
}
