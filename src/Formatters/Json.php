<?php

namespace Differ\Formatters\Json;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getNewValue;

function renderJson(array $array, $tab = "")
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
                $oldValue = getOldValue($node);
            }
            if (is_object(getNewValue($node))) {
            } else {
                $newValue = getNewValue($node);
            }
            $acc[] = "{$tab}  + {$key}: {$newValue}";
            $acc[] = "{$tab}  - {$key}: {$oldValue}";
        }
        
        if (isChildren($node)) {
            $acc[] = "{$tab}    {$key}: {";
            $acc[] = renderJson(getChildren($node), $tab . "    ");
            $acc[] = "{$tab}    }";
        }
        
        if (isSame($node)) {
            if (is_object(getNewValue($node))) {
                $acc[] = "{$tab}    {$key}: {";
                $acc[] = renderObject(getBewValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $value = getNewValue($node);
                $acc[] = "{$tab}    {$key}: {$value}";
            }
        }
        
        if (isRemoved($node)) {
            if (is_object(getOldValue($node))) {
                $acc[] = "{$tab}  - {$key}: {";
                $acc[] = renderObject(getOldValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $oldValue = getOldValue($node);
                $acc[] = "{$tab}  - {$key}: {$oldValue}";
            }
        }
        
        if (isAdded($node)) {
            if (is_object(getNewValue($node))) {
                $acc[] = "{$tab}  + {$key}: {";
                $acc[] = renderObject(getNewValue($node), $tab);
                $acc[] = "{$tab}    }";
            } else {
                $newValue = getNewValue($node);
                $acc[] = "{$tab}  + {$key}: {$newValue}";
            }
        }

        return $acc;
    }, []);
    
    if ($tab === "") {
        $result[] = "}";
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
            $result[] = "{$tab}        {$key}: {$value}";
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

function getChildren($node)
{
    return isset($node['children']) ? $node['children'] : null;
}
