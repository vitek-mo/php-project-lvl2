<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

function getExpectedResult($outputFormat)
{
    $expectedPath = $outputFormat === 'pretty' ? __DIR__ . '/fixtures/result' : __DIR__ . '/fixtures/result.' . $outputFormat;
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
    
    public function testGendiff($expectedResult, $actualResult)
    {
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function provider()
    {
        return array(
            array(getExpectedResult('pretty'), getActualResult('json', 'json', 'pretty')),
            array(getExpectedResult('pretty'), getActualResult('yml', 'yml', 'pretty')),
            array(getExpectedResult('pretty'), getActualResult('json', 'yml', 'pretty')),
            array(getExpectedResult('pretty'), getActualResult('yml', 'json', 'pretty')),
            array(getExpectedResult('plain'), getActualResult('json', 'json', 'plain')),
            array(getExpectedResult('json'), getActualResult('json', 'json', 'json'))
        );
    }
}
