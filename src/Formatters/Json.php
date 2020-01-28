<?php

namespace Differ\Formatters\Json;

function renderJson($dif)
{
    return json_encode($dif, JSON_PRETTY_PRINT);
}
