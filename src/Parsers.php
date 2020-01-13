<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;


function parse($contents, $formats)
{
    [$content1, $content2] = $contents;
    [$format1, $format2] = $formats;
    $parsed1 = null;
    $parsed2 = null;
    if ($format1 === 'json') {
        $parsed1 = json_decode($content1);
    }
    if ($format2 === 'json') {
        $parsed2 = json_decode($content2);
    }
    
    if ($format1 === 'yml') {
        $parsed1 = Yaml::parse($content1, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    if ($format2 === 'yml') {
        $parsed2 = Yaml::parse($content2, Yaml::PARSE_OBJECT_FOR_MAP);
    }
    return [$parsed1, $parsed2];
}
