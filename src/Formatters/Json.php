<?php

namespace Differ\Formatters\Json;

function renderJson($diff)
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
