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
                $acc[] = stringify($indent, $key, "+", getNewValue($node));
                $acc[] = stringify($indent, $key, "-", getOldValue($node));
                return $acc;
            case 'nested':
                //вот тут ещё сомневаюсь, нужно ли и как в stringify значение типа Nested обрабатывать.
                //тип возвращаемого значения getChildren будет array (массив из Node)
                //можно сделать по этому отдельный case, в котором уже рекурсивно будет renderPretty вызываться
                $acc[] = "{$indent}    {$key}: {";
                $acc[] = renderPretty(getChildren($node), $indent . "    ");
                $acc[] = "{$indent}    }";
                return $acc;
            case 'same':
                $sign = ' ';
                $nodeValue = getNewValue($node);
                break;
            case 'removed':
                $sign = '-';
                $nodeValue = getOldValue($node);
                break;
            case 'added':
                $sign = '+';
                $nodeValue = getNewValue($node);
                break;
        }
        $acc[] = stringify($indent, $key, $sign, $nodeValue);
        return $acc;
    }, []);

    $stringified = implode("\n", flattenAll($result));

    $closingBrace = $indent === "" ? "\n}\n" : "";
    
    return "{$openingBrace}{$stringified}{$closingBrace}";
}

function stringify($indent, $key, $sign, $nodeValue)
{
    switch (gettype($nodeValue)) {
        case 'boolean':
            $booleanStringified = $nodeValue ? "true" : "false";
            $result = "{$indent}  {$sign} {$key}: {$booleanStringified}";
            break;
        case 'object':
            $objectVars = get_object_vars($nodeValue);
            $openingString = "{$indent}  {$sign} {$key}: {\n";
            $objectKeys = array_keys($objectVars);
            $valueStrings = array_map(function ($objectKey) use ($objectVars, $indent) {
                return stringify($indent . "    ", $objectKey, " ", $objectVars[$objectKey]);
            }, $objectKeys);
            $valueString = implode("\n", $valueStrings);
            $closingString = "{$indent}    }";
            $result = "{$openingString}{$valueString}\n{$closingString}";
            break;
        default:
            $result = "{$indent}  {$sign} {$key}: {$nodeValue}";
            break;
    }
    return $result;
}
