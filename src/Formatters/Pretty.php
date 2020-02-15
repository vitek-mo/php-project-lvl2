<?php

namespace Differ\Formatters\Pretty;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\getKey;
use function Differ\Formatters\Common\getNodeType;
use function Differ\Formatters\Common\getNewValue;
use function Differ\Formatters\Common\getOldValue;
use function Differ\Formatters\Common\getChildren;
use function Differ\Formatters\Common\ifBoolMakeString;

function renderPretty(array $array, $indent = "")
{
    $openingBrace = $indent === "" ? "{\n" : "";
    
    $result = array_reduce($array, function ($acc, $node) use ($indent) {
        $key = getKey($node);
        $type = getNodeType($node);
        switch ($type) {
            case 'changed':
                $stringifiedNew = stringify(getNewValue($node), $indent);
                $stringifiedOld = stringify(getOldValue($node), $indent);
                $acc[] = "{$indent}  + {$key}: {$stringifiedNew}";
                $acc[] = "{$indent}  - {$key}: {$stringifiedOld}";
                break;
            case 'nested':
                $acc[] = "{$indent}    {$key}: {";
                $acc[] = renderPretty(getChildren($node), $indent . "    ");
                $acc[] = "{$indent}    }";
                break;
            case 'same':
                $stringified = stringify(getNewValue($node), $indent);
                $acc[] = "{$indent}    {$key}: {$stringified}";
                break;
            case 'removed':
                $stringified = stringify(getOldValue($node), $indent);
                $acc[] = "{$indent}  - {$key}: {$stringified}";
                break;
            case 'added':
                $stringified = stringify(getNewValue($node), $indent);
                $acc[] = "{$indent}  + {$key}: {$stringified}";
                break;
        }
        return $acc;
    }, []);

    $flattenedResult = implode("\n", flattenAll($result));

    $closingBrace = $indent === "" ? "\n}\n" : "";
    
    return "{$openingBrace}{$flattenedResult}{$closingBrace}";
}

function stringify($nodeValue, $indent)
{
    switch (gettype($nodeValue)) {
        case 'boolean':
            return $nodeValue ? "true" : "false";
            break;
        case 'object':
            $objectVars = get_object_vars($nodeValue);
            $objectKeys = array_keys($objectVars);
            $valueStrings = array_map(function ($objectKey) use ($objectVars, $indent) {
                $stringified = stringify($objectVars[$objectKey], $indent . "    ");
                return "{$indent}        {$objectKey}: {$stringified}";
            }, $objectKeys);
            $valueString = implode("\n", $valueStrings);
            return "{\n{$valueString}\n{$indent}    }";
            break;
        default:
            return $nodeValue;
            break;
    }
}
