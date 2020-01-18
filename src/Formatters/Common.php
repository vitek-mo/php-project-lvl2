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
    if (isset($node['type'])) {
        if ($node['type'] === 'children') {
            return true;
        }
    }
    return false;
}

function getKey($node)
{
    return isset($node['key']) ? $node['key'] : null;
}

function getType($node)
{
    return isset($node['type']) ? $node['type'] : null;
}

function getNewValue($node)
{
    return isset($node['newValue']) ? $node['newValue'] : null;
}

function getOldValue($node)
{
    return isset($node['oldValue']) ? $node['oldValue'] : null;
}

function getChildren($node)
{
    return isset($node['children']) ? $node['children'] : null;
}