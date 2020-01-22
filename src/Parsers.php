<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($content, $format)
{
    switch ($format) {
        case 'json':
            $parsed = json_decode($content);
            break;
        case 'yml':
        case 'yaml':
            $parsed = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
            break;
    }
    return $parsed;
}
