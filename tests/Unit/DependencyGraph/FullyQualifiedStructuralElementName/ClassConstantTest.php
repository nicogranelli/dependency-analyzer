<?php
declare(strict_types=1);

namespace Tests\Unit\DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName;

use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Base;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Class_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\ClassConstant;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Constant;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Function_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Interface_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Method;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Namespace_;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Property;
use DependencyAnalyzer\DependencyGraph\FullyQualifiedStructuralElementName\Trait_;
use Tests\TestCase;

class ClassConstantTest extends TestCase
{
    public function provideInclude()
    {
        return [
            'class1' => [new Class_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1'), false],
            'class2' => [new Class_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2'), false],
            'method1' => [new Method('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::someMethod()'), false],
            'method2' => [new Method('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::someMethod()'), false],
            'property1' => [new Property('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::$someProperty'), false],
            'property2' => [new Property('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::$someProperty'), false],
            'class constant1' => [new ClassConstant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::SOME_CONSTANT'), true],
            'class constant2' => [new ClassConstant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass2::SOME_CONSTANT'), false],
            'namespace1' => [new Namespace_('\Tests\Fixtures\FullyQualifiedStructuralElementName\\'), false],
            'namespace2' => [new Namespace_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass\\'), false],
            'interface' => [new Interface_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeInterface1'), false],
            'trait' => [new Trait_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeTrait1'), false],
            'function' => [new Function_('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeFunction()'), false],
            'constant' => [new Constant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SOME_CONSTANT'), false],
        ];
    }

    /**
     * @param Base $target
     * @param bool $expected
     * @dataProvider provideInclude
     */
    public function testInclude(Base $target, bool $expected)
    {
        $sut = new ClassConstant('\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass1::SOME_CONSTANT');

        $this->assertSame($expected, $sut->include($target));
    }

    public function provideGetFullyQualifiedNamespaceNameAsArray()
    {
        return [
            ['\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass::SOME_CONSTANT', ['Tests', 'Fixtures', 'FullyQualifiedStructuralElementName']],
            ['\SomeClass::SOME_CONSTANT', []]
        ];
    }

    /**
     * @param string $classConstantName
     * @param array $expected
     * @dataProvider provideGetFullyQualifiedNamespaceNameAsArray
     */
    public function testGetFullyQualifiedNamespaceNameAsArray(string $classConstantName, array $expected)
    {
        $sut = new ClassConstant($classConstantName);

        $this->assertSame($expected, $sut->getFullyQualifiedNamespaceNameAsArray());
    }

    public function provideGetFullyQualifiedClassNameAsArray()
    {
        return [
            ['\Tests\Fixtures\FullyQualifiedStructuralElementName\SomeClass::SOME_CONSTANT', ['Tests', 'Fixtures', 'FullyQualifiedStructuralElementName', 'SomeClass']],
            ['\SomeClass::SOME_CONSTANT', ['SomeClass']]
        ];
    }

    /**
     * @param string $className
     * @param array $expected
     * @dataProvider provideGetFullyQualifiedClassNameAsArray
     */
    public function testGetFullyQualifiedClassNameAsArray(string $className, array $expected)
    {
        $sut = new ClassConstant($className);

        $this->assertSame($expected, $sut->getFullyQualifiedClassNameAsArray());
    }
}
