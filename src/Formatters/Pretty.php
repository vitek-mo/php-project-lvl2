<?php

namespace Differ\Formatters\Pretty;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;
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
            case 'children':
                $acc[] = "{$tab}    {$key}: {";
                $acc[] = renderPretty(getChildren($node), $tab . "    ");
                $acc[] = "{$tab}    }";
                break;
            case 'same':
                if (is_object(getNewValue($node))) {
                    $acc[] = "{$tab}    {$key}: {";
                    $acc[] = renderObject(getNewValue($node), $tab);
                    $acc[] = "{$tab}    }";
                } else {
                    $value = checkForBool(getNewValue($node));
                    $acc[] = "{$tab}    {$key}: {$value}";
                }
                break;
            case 'removed':
                if (is_object(getOldValue($node))) {
                    $acc[] = "{$tab}  - {$key}: {";
                    $acc[] = renderObject(getOldValue($node), $tab);
                    $acc[] = "{$tab}    }";
                } else {
                    $oldValue = checkForBool(getOldValue($node));
                    $acc[] = "{$tab}  - {$key}: {$oldValue}";
                }
                break;
            case 'added':
                if (is_object(getNewValue($node))) {
                    $acc[] = "{$tab}  + {$key}: {";
                    $acc[] = renderObject(getNewValue($node), $tab);
                    $acc[] = "{$tab}    }";
                } else {
                    $newValue = checkForBool(getNewValue($node));
                    $acc[] = "{$tab}  + {$key}: {$newValue}";
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
