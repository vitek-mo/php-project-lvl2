<?php

namespace Differ\Formatters\Common;

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
