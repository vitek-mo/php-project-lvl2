<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($contents, $format)
{
    [$content1, $content2] = $contents;
    if ($format === 'json') {
        $parsed1 = json_decode($content1);
        $parsed2 = json_decode($content2);
    }
    if ($format === 'yml') {
        $parsed1 = Yaml::parse($content1, Yaml::PARSE_OBJECT_FOR_MAP);
        $parsed2 = Yaml::parse($content2, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    return [$parsed1, $parsed2];
}
