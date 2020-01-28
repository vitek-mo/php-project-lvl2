<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

function getExpectedResult($outputFormat)
{
    $expectedPath = __DIR__ . '/fixtures/output/result.' . $outputFormat;
    return file_get_contents($expectedPath);
}

function getActualResult($inputFormatBefore, $inputFormatAfter, $outputFormat)
{
    $inputPathBefore = __DIR__ . '/fixtures/input/before.' . $inputFormatBefore;
    $inputPathAfter = __DIR__ . '/fixtures/input/after.' . $inputFormatAfter;
    return genDiff($inputPathBefore, $inputPathAfter, $outputFormat);
}

class DifferTest extends TestCase
{
    /**
    * @dataProvider provider
    */
    
    public function testGendiff($outputFormat, $inputFormatBefore, $inputFormatAfter)
    {
        $expectedResult = getExpectedResult($outputFormat);
        $actualResult = getActualResult($inputFormatBefore, $inputFormatAfter, $outputFormat);
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function provider()
    {
        return [
            ['pretty', 'json', 'json'],
            ['pretty', 'yml', 'yml'],
            ['pretty', 'json', 'yml'],
            ['pretty', 'yml', 'json'],
            ['plain', 'json', 'json'],
            ['json', 'json', 'json']
        ];
    }
}
