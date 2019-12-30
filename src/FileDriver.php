<?php

namespace Differ\FileDriver;

function getFilesContent($filePaths)
{
    [$path1, $path2] = $filePaths;
    if(!is_readable($path1)) {
        throw new \Exception("'{$path1}' is not exist or not readable.");
    }
    if(!is_readable($path2)) {
        throw new \Exception("'{$path2}' is not exist or not readable.");
    }
    return [file_get_contents($path1), file_get_contents($path2)];
}
