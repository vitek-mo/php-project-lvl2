<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDiffer()
    {
        $path1 = __DIR__ . '/fixtures/file1';
        $path2 = __DIR__ . '/fixtures/file2';
        
        $expectedResult = file_get_contents(__DIR__ . '/fixtures/result');
        
        $this->assertEquals($expectedResult, genDiff($path1, $path2));
    }
}