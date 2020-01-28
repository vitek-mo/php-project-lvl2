<?php

namespace Differ\Formatters\Common;

function checkForBool($value)
{
    if (is_bool($value)) {
        return $value ? "true" : "false";
    } else {
        return $value;
    }
}

function isChildren($node)
{
    return (getType($node) === 'children') ?: false;
}

function getKey($node)
{
    return $node['key'];
}

function getType($node)
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
