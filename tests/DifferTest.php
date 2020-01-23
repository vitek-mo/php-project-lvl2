<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

function getExpectedResult($outputFormat)
{
    $basePath = __DIR__ . '/fixtures/result';
    $expectedPath = $outputFormat === 'pretty' ? $basePath : $basePath . '.' . $outputFormat;
    return file_get_contents($expectedPath);
}

function getActualResult($inputFormatBefore, $inputFormatAfter, $outputFormat)
{
    $inputPathBefore = __DIR__ . '/fixtures/before.' . $inputFormatBefore;
    $inputPathAfter = __DIR__ . '/fixtures/after.' . $inputFormatAfter;
    return genDiff($inputPathBefore, $inputPathAfter, $outputFormat);
}

class DifferTest extends TestCase
{
    /**
    * @dataProvider provider
    */
    
    public function testGendiff($outputFormat, $inputFormatBefore, $inputFormatAfter)
    {
        $this->assertEquals(getExpectedResult($outputFormat), getActualResult($inputFormatBefore, $inputFormatAfter, $outputFormat));
    }
    
    public function provider()
    {
        return array(
            array('pretty', 'json', 'json'),
            array('pretty', 'yml', 'yml'),
            array('pretty', 'json', 'yml'),
            array('pretty', 'yml', 'json'),
            array('plain', 'json', 'json'),
            array('json', 'json', 'json')
        );
    }
}
