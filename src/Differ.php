<?php

namespace Differ;

$devPath = '__DIR__/../vendor/autoload.php';
$absoluteDevPath = '/home/viktor/hexlet/PHP/projects/php-project-lvl2/vendor/autoload.php';

if (file_exists($devPath)) {
    require_once($devPath);
} else {
    require_once($absoluteDevPath);
}

//use function Differ\DocoptDriver\docoptInit;
//use function Differ\DocoptDriver\getPaths;

use function Differ\FileDriver\getFilesContent;

/*
const VERSION = 'Generate diff 0.1';
const DOC = <<<DOC


Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]

DOC;
*/

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


