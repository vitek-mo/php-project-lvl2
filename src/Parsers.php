<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($contents, $format)
{
    if ($format === 'json') {
        [$json1, $json2] = $contents;
        $parsed1 = json_decode($json1, true);
        $parsed2 = json_decode($json2, true);
        return [$parsed1, $parsed2];
    }
    if ($format === 'yml') {
        [$yml1, $yml2] = $contents;
        $parsed1 = Yaml::parse($yml1);
        $parsed2 = Yaml::parse($yml2);
        return [$parsed1, $parsed2];
    }
}
