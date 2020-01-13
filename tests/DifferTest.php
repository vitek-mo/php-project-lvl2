<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Analyzer\genDiff;

class DifferTest extends TestCase
{
    public function testPlainJson()
    {
        $expectedPath = __DIR__ . '/fixtures/result.json';
        $beforePath = __DIR__ . '/fixtures/before.json';
        $afterPath = __DIR__ . '/fixtures/after.json';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testPlainYml()
    {
        $expectedPath = __DIR__ . '/fixtures/result.json';
        $beforePath = __DIR__ . '/fixtures/before.yml';
        $afterPath = __DIR__ . '/fixtures/after.yml';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testPlainJsonYml()
    {
        $expectedPath = __DIR__ . '/fixtures/result.json';
        $beforePath = __DIR__ . '/fixtures/before.json';
        $afterPath = __DIR__ . '/fixtures/after.yml';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testPlainYmlJson()
    {
        $expectedPath = __DIR__ . '/fixtures/result.json';
        $beforePath = __DIR__ . '/fixtures/before.yml';
        $afterPath = __DIR__ . '/fixtures/after.json';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testNestedJson()
    {
        $expectedPath = __DIR__ . '/fixtures/result2.json';
        $beforePath = __DIR__ . '/fixtures/before2.json';
        $afterPath = __DIR__ . '/fixtures/after2.json';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testNestedYml()
    {
        $expectedPath = __DIR__ . '/fixtures/result2.json';
        $beforePath = __DIR__ . '/fixtures/before2.yml';
        $afterPath = __DIR__ . '/fixtures/after2.yml';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testNestedJsonYml()
    {
        $expectedPath = __DIR__ . '/fixtures/result2.json';
        $beforePath = __DIR__ . '/fixtures/before2.json';
        $afterPath = __DIR__ . '/fixtures/after2.yml';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    public function testNestedYmlJson()
    {
        $expectedPath = __DIR__ . '/fixtures/result2.json';
        $beforePath = __DIR__ . '/fixtures/before2.yml';
        $afterPath = __DIR__ . '/fixtures/after2.json';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
    
    public function testNestedPlain()
    {
        $expectedPath = __DIR__ . '/fixtures/result2.plain';
        $beforePath = __DIR__ . '/fixtures/before2.json';
        $afterPath = __DIR__ . '/fixtures/after2.json';
        $expectedResult = file_get_contents($expectedPath);
        $this->assertEquals($expectedResult, genDiff($beforePath, $afterPath, 'json'));
    }
}
