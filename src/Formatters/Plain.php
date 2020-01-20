<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getType;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getOldValue;

function renderPlain($dif, $path = '')
{
    $result = [];
    $result[] = array_reduce($dif, function ($acc, $node) use ($path) {
        if (isChildren($node)) {
            $key = getKey($node);
            $intPath = ($path === "") ? $key : "{$path}.{$key}";
            $acc[] = renderPlain($node['children'], $intPath);
            return $acc;
        } else {
            if (getType($node) !== 'same') {
                $key = getKey($node);
                $property = $path === '' ? $key : "{$path}.{$key}";
                $ending = "";
                $value = "";
                $action = getType($node);
                if (is_object(getNewValue($node))) {
                    $value = "complex value";
                    $ending = " with value: '{$value}'";
                } else {
                    if (getType($node) === "added") {
                        $value = getNewValue($node);
                        $ending = " with value: '{$value}'";
                    }
                    if (getType($node) === "changed") {
                        $oldValue = getOldValue($node);
                        $newValue = getNewValue($node);
                        $ending = ". From '{$oldValue}' to '{$newValue}'";
                    }
                }
            } else {
                return $acc;
            }
            $acc[] = "Property '{$property}' was {$action}{$ending}";
            return $acc;
        }
    }, []);
    if ($path === '') {
        $result[] = "";
    }
    return implode("\n", flattenAll($result));
}
