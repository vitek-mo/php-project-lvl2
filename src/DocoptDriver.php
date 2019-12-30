<?php

namespace Differ\DocoptDriver;

//This will work as usage helper when file is loaded. It will print correct usage in case of incorrect
//input. You can change DOC constant above to modify it's bahaviour. It will automatically parse it and
//will expect command template as you composed in DOC. $args will contain arguments and options for further use.

function docoptInit($doc, $params)
{
    return \Docopt::handle($doc, $params);
}

function getPaths($args)
{
    $path1 = $args->args['<firstFile>'];
    $path2 = $args->args['<secondFile>'];
    return [$path1, $path2];
}
