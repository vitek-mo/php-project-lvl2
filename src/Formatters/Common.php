<?php

namespace Differ\Formatters\Common;

function ifBoolMakeString($value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } else {
        return $value;
    }
}

function isNested($node)
{
    return (getType($node) === 'nested') ?: false;
}

function getKey($node)
{
    return $node['key'];
}

function getNodeType($node)
{
    return $node['type'];
}

function getNewValue($node)
{
    return $node['newValue'];
}

function getOldValue($node)
{
    return $node['oldValue'];
}

function getChildren($node)
{
    return $node['children'];
}
