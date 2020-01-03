<?php

namespace Differ;

/*
$devPath = '__DIR__/../vendor/autoload.php';
$absoluteDevPath = '/home/viktor/hexlet/PHP/projects/php-project-lvl2/vendor/autoload.php';

if (file_exists($devPath)) {
    require_once($devPath);
} else {
    require_once($absoluteDevPath);
}
*/

use function Differ\FileDriver\getFilesContent;

function genDiff($path1, $path2)
{
    $paths = [$path1, $path2];
    try {
        $contents = getFilesContent($paths);
    } catch (\Exception $e) {
        print_r("Error: {$e->getMessage()}\n");
        return;
    }
    return \Differ\Analyzer\genDiff($contents);
}
