<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

class DifferTest extends TestCase
{
    public function testDifferJson()
    {   $jsonExpectedPath = __DIR__ . '/fixtures/result.json';
        $jsonBeforePath = __DIR__ . '/fixtures/before.json';
        $jsonAfterPath = __DIR__ . '/fixtures/after.json';
        $expectedResult = file_get_contents($jsonExpectedPath);
        
        $this->assertEquals($expectedResult, genDiff($jsonBeforePath, $jsonAfterPath, 'json'));
    }
}
