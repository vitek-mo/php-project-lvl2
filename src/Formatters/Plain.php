<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isNested;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getNodeType;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getOldValue;
use function Differ\Formatters\Common\getChildren;

function renderPlain($diff, $path = '')
{
    $result = array_map(function ($node) use ($path) {
        $type = getNodeType($node);
        $key = getKey($node);
        $fullPath = "{$path}{$key}";
        switch ($type) {
            case 'nested':
                $currentPath = ($path === '') ? "{$key}." : "{$path}{$key}.";
                return renderPlain(getChildren($node), $currentPath);
                break;
            case 'removed':
                return "Property '{$fullPath}' was removed";
                break;
            case 'added':
                $value = stringify(getNewValue($node));
                return "Property '{$fullPath}' was added with value: '{$value}'";
                break;
            case 'changed':
                $oldValue = stringify(getOldValue($node));
                $newValue = stringify(getNewValue($node));
                return "Property '{$fullPath}' was changed. From '{$oldValue}' to '{$newValue}'";
                break;
            case 'same':
                return;
                break;
        }
    }, $diff);
    $filteredResult = array_filter($result, function ($sentence) {
        return $sentence;
    });

    $flattenedResult = implode("\n", $filteredResult);
    return $path === '' ? $flattenedResult . "\n" : $flattenedResult;
}

function stringify($value)
{
    return is_object($value) ? 'complex value' : $value;
}
