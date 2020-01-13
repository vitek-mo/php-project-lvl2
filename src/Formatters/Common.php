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