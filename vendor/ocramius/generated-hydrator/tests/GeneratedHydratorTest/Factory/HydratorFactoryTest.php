<?php

declare(strict_types=1);

namespace GeneratedHydratorTest\Factory;

use CodeGenerationUtils\Autoloader\AutoloaderInterface;
use CodeGenerationUtils\GeneratorStrategy\GeneratorStrategyInterface;
use CodeGenerationUtils\Inflector\ClassNameInflectorInterface;
use CodeGenerationUtils\Inflector\Util\UniqueIdentifierGenerator;
use GeneratedHydrator\ClassGenerator\HydratorGenerator;
use GeneratedHydrator\Factory\HydratorFactory;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \GeneratedHydrator\Factory\HydratorFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class HydratorFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $inflector;

    /**
     * @var \GeneratedHydrator\Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->inflector = $this->createMock(ClassNameInflectorInterface::class);
        $this->config    = $this
            ->getMockBuilder('GeneratedHydrator\\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        $this
            ->config
            ->expects(self::any())
            ->method('getClassNameInflector')
            ->will(self::returnValue($this->inflector));
    }

    /**
     * {@inheritDoc}
     *
     * @covers \GeneratedHydrator\Factory\HydratorFactory::__construct
     * @covers \GeneratedHydrator\Factory\HydratorFactory::getHydratorClass
     */
    public function testWillSkipAutoGeneration()
    {
        $className = UniqueIdentifierGenerator::getIdentifier('foo');

        $this->config->expects(self::any())->method('getHydratedClassName')->will(self::returnValue($className));
        $this->config->expects(self::any())->method('doesAutoGenerateProxies')->will(self::returnValue(false));
        $this
            ->inflector
            ->expects(self::any())
            ->method('getUserClassName')
            ->with($className)
            ->will(self::returnValue('GeneratedHydratorTestAsset\\BaseClass'));

        $this
            ->inflector
            ->expects(self::once())
            ->method('getGeneratedClassName')
            ->with('GeneratedHydratorTestAsset\\BaseClass')
            ->will(self::returnValue('GeneratedHydratorTestAsset\\EmptyClass'));

        $factory        = new HydratorFactory($this->config);
        $generatedClass = $factory->getHydratorClass();

        self::assertInstanceOf('GeneratedHydratorTestAsset\\EmptyClass', new $generatedClass);
    }

    /**
     * {@inheritDoc}
     *
     * @covers \GeneratedHydrator\Factory\HydratorFactory::__construct
     * @covers \GeneratedHydrator\Factory\HydratorFactory::getHydratorClass
     *
     * NOTE: serious mocking going on in here (a class is generated on-the-fly) - careful
     */
    public function testWillTryAutoGeneration()
    {
        $className          = UniqueIdentifierGenerator::getIdentifier('foo');
        $generatedClassName = UniqueIdentifierGenerator::getIdentifier('bar');
        $generator          = $this->createMock(GeneratorStrategyInterface::class);
        $autoloader         = $this->createMock(AutoloaderInterface::class);

        $this->config->expects(self::any())->method('getHydratedClassName')->will(self::returnValue($className));
        $this->config->expects(self::any())->method('doesAutoGenerateProxies')->will(self::returnValue(true));
        $this->config->expects(self::any())->method('getGeneratorStrategy')->will(self::returnValue($generator));
        $this->config->expects(self::any())->method('getHydratorGenerator')->willReturn(new HydratorGenerator());
        $this
            ->config
            ->expects(self::any())
            ->method('getGeneratedClassAutoloader')
            ->will(self::returnValue($autoloader));

        $generator
            ->expects(self::once())
            ->method('generate')
            ->with(self::isType('array'));

        // simulate autoloading
        $autoloader
            ->expects(self::once())
            ->method('__invoke')
            ->with($generatedClassName)
            ->willReturnCallback(function () use ($generatedClassName) : bool {
                eval('class ' . $generatedClassName . ' {}');

                return true;
            });

            $this
            ->inflector
            ->expects(self::once())
            ->method('getGeneratedClassName')
            ->with('GeneratedHydratorTestAsset\\BaseClass')
            ->will(self::returnValue($generatedClassName));

            $this
            ->inflector
            ->expects(self::once())
            ->method('getUserClassName')
            ->with($className)
            ->will(self::returnValue('GeneratedHydratorTestAsset\\BaseClass'));

            $factory        = new HydratorFactory($this->config);
        /* @var $generatedClass \GeneratedHydratorTestAsset\LazyLoadingMock */
            $generatedClass = $factory->getHydratorClass();

            self::assertInstanceOf($generatedClassName, new $generatedClass);
    }
}
