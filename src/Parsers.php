<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($contents, $format)
{
    [$content1, $content2] = $contents;
    if ($format === 'json') {
        $parsed1 = json_decode($content1, true);
        $parsed2 = json_decode($content2, true);
    }
    if ($format === 'yml') {
        $parsed1 = Yaml::parse($content1);
        $parsed2 = Yaml::parse($content2);
    }
    return [$parsed1, $parsed2];
}
