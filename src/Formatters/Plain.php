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
        switch ($type) {
            case 'nested':
                $currentPath = ($path === '') ? "{$key}." : "{$path}{$key}.";
                return renderPlain(getChildren($node), $currentPath);
                break;
            case 'removed':
                $typeSpecificPart = "";
                break;
            case 'added':
                $value = is_object(getNewValue($node)) ? 'complex value' : getNewValue($node);
                $typeSpecificPart = " with value: '{$value}'";
                break;
            case 'changed':
                $oldValue = is_object(getOldValue($node)) ? 'complex value' : getOldValue($node);
                $newValue = is_object(getNewValue($node)) ? 'complex value' : getNewValue($node);
                $typeSpecificPart = ". From '{$oldValue}' to '{$newValue}'";
                break;
            case 'same':
                return;
                break;
        }
        $fullPath = "{$path}{$key}";
        return "Property '{$fullPath}' was {$type}{$typeSpecificPart}";
    }, $diff);
    $filteredResult = array_filter($result, function ($sentence) {
        return $sentence;
    });

    $flattenedResult = implode("\n", $filteredResult);
    return $path === '' ? $flattenedResult . "\n" : $flattenedResult;
}
