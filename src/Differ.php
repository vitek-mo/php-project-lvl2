<?php

namespace Differ;

use function Differ\FileDriver\getFilesContent;

function genDiff($path1, $path2, $format)
{
    $paths = [$path1, $path2];
    try {
        $contents = getFilesContent($paths);
    } catch (\Exception $e) {
        print_r("Error: {$e->getMessage()}\n");
        return;
    }
    return \Differ\Analyzer\genDiff($contents, $format);
}
