<?php

namespace Differ\Formatters\Plain;

use function Funct\Collection\flattenAll;
use function Differ\Formatters\Common\isChildren;

function renderPlain($dif, $propertyPath = "")
{
    $result = [];
    $result[] = array_reduce($dif, function($acc, $node) use ($propertyPath){
        if (isChildren($node)) {
            $propertyPath = "{$propertyPath}{$node['key']}.";
            $acc[] = renderPlain($node, $propertyPath);
        }
        return $acc;
    }, []);
    return flattenAll($result);
}