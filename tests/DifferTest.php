<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

class DifferTest extends TestCase
{
    public function testDifferPlainJson()
    {
        $jsonExpectedPath = __DIR__ . '/fixtures/result.json';
        $jsonBeforePath = __DIR__ . '/fixtures/before.json';
        $jsonAfterPath = __DIR__ . '/fixtures/after.json';
        $expectedResult = file_get_contents($jsonExpectedPath);
        $this->assertEquals($expectedResult, genDiff($jsonBeforePath, $jsonAfterPath, 'json'));
    }
    
    public function testDifferPlainYml()
    {
        $jsonExpectedPath = __DIR__ . '/fixtures/result.json';
        $ymlBeforePath = __DIR__ . '/fixtures/before.yml';
        $ymlAfterPath = __DIR__ . '/fixtures/after.yml';
        $expectedResult = file_get_contents($jsonExpectedPath);
        
        $this->assertEquals($expectedResult, genDiff($ymlBeforePath, $ymlAfterPath, 'yml'));
    }
    
    public function testDifferJson()
    {
        $jsonExpectedPath = __DIR__ . '/fixtures/result2.json';
        $jsonBeforePath = __DIR__ . '/fixtures/before2.json';
        $jsonAfterPath = __DIR__ . '/fixtures/after2.json';
        $expectedResult = file_get_contents($jsonExpectedPath);
        
        $this->assertEquals($expectedResult, genDiff($jsonBeforePath, $jsonAfterPath, 'json'));
    }
    
    public function testDifferYml()
    {
        $jsonExpectedPath = __DIR__ . '/fixtures/result2.json';
        $ymlBeforePath = __DIR__ . '/fixtures/before2.yml';
        $ymlAfterPath = __DIR__ . '/fixtures/after2.yml';
        $expectedResult = file_get_contents($jsonExpectedPath);
        
        $this->assertEquals($expectedResult, genDiff($ymlBeforePath, $ymlAfterPath, 'yml'));
    }
}
