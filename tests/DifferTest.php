<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

function pathsBuild($firstFormat, $secondFormat, $outputFormat)
{
    $beforePath = __DIR__ . '/fixtures/before.' . $firstFormat;
    $afterPath = __DIR__ . '/fixtures/after.' . $secondFormat;
    if ($outputFormat === 'pretty') {
        $expectedPath = __DIR__ . '/fixtures/result';
    } else {
        $expectedPath = __DIR__ . '/fixtures/result.' . $outputFormat;
    }
    return [$beforePath, $afterPath, $expectedPath];
}

function getData($firstFormat, $secondFormat, $outputFormat)
{
    [$beforePath, $afterPath, $expectedPath] = pathsBuild($firstFormat, $secondFormat, $outputFormat);
    $expectedResult = file_get_contents($expectedPath);
    $actualResult = genDiff($beforePath, $afterPath, $outputFormat);
    return [$expectedResult, $actualResult];
}

class DifferTest extends TestCase
{
    public function testNestedJson()
    {
        [$expectedResult, $actualResult] = getData('json', 'json', 'pretty');
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function testNestedYml()
    {
        [$expectedResult, $actualResult] = getData('yml', 'yml', 'pretty');
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function testNestedJsonYml()
    {
        [$expectedResult, $actualResult] = getData('json', 'yml', 'pretty');
        $this->assertEquals($expectedResult, $actualResult);
    }
    public function testNestedYmlJson()
    {
        [$expectedResult, $actualResult] = getData('yml', 'json', 'pretty');
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function testNestedToPlain()
    {
        [$expectedResult, $actualResult] = getData('json', 'json', 'plain');
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function testNestedToJson()
    {
        [$expectedResult, $actualResult] = getData('json', 'json', 'json');
        $this->assertEquals($expectedResult, $actualResult);
    }
}
