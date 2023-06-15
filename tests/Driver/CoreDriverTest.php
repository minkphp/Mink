<?php

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Element\NodeElement;
use PHPUnit\Framework\TestCase;

class CoreDriverTest extends TestCase
{
    public function testNoExtraMethods()
    {
        $interfaceRef = new \ReflectionClass('Behat\Mink\Driver\DriverInterface');
        $coreDriverRef = new \ReflectionClass('Behat\Mink\Driver\CoreDriver');

        foreach ($coreDriverRef->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->assertTrue(
                $interfaceRef->hasMethod($method->getName()),
                sprintf('CoreDriver should not implement methods which are not part of the DriverInterface but %s found', $method->getName())
            );
        }
    }

    public function testCreateNodeElements()
    {
        $driver = $this->getMockBuilder('Behat\Mink\Driver\CoreDriver')
            ->setMethods(array('findElementXpaths'))
            ->getMockForAbstractClass();

        $session = $this->getMockBuilder('Behat\Mink\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $driver->setSession($session);

        $driver->expects($this->once())
            ->method('findElementXpaths')
            ->with('xpath')
            ->willReturn(array('xpath1', 'xpath2'));

        /** @var NodeElement[] $elements */
        $elements = $driver->find('xpath');

        $this->assertIsArray($elements);

        $this->assertCount(2, $elements);
        $this->assertContainsOnlyInstancesOf('Behat\Mink\Element\NodeElement', $elements);

        $this->assertSame('xpath1', $elements[0]->getXpath());
        $this->assertSame('xpath2', $elements[1]->getXpath());
    }

    /**
     * @dataProvider getDriverInterfaceMethods
     */
    public function testInterfaceMethods(\ReflectionMethod $method)
    {
        $refl = new \ReflectionClass('Behat\Mink\Driver\CoreDriver');

        $coreDriverMethod = $refl->getMethod($method->getName());

        $this->assertFalse(
            $coreDriverMethod->isAbstract(),
            sprintf('CoreDriver should implement a dummy %s method', $method->getName())
        );

        if ('setSession' === $method->getName()) {
            return; // setSession is actually implemented, so we don't expect an exception here.
        }

        $driver = $this->getMockForAbstractClass('Behat\Mink\Driver\CoreDriver');

        $this->expectException('Behat\Mink\Exception\UnsupportedDriverActionException');

        $coreDriverMethod->invokeArgs($driver, $this->getArguments($method));
    }

    public function getDriverInterfaceMethods()
    {
        $ref = new \ReflectionClass('Behat\Mink\Driver\DriverInterface');

        return array_map(function ($method) {
            return array($method);
        }, $ref->getMethods());
    }

    /**
     * @return list<mixed>
     */
    private function getArguments(\ReflectionMethod $method)
    {
        $arguments = array();

        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->getArgument($parameter);
        }

        return $arguments;
    }

    /**
     * @return mixed
     */
    private function getArgument(\ReflectionParameter $argument)
    {
        if ($argument->isOptional()) {
            return $argument->getDefaultValue();
        }

        if ($argument->allowsNull()) {
            return null;
        }

        $type = $argument->getType();

        if ($type instanceof \ReflectionNamedType) {
            switch ($type->getName()) {
                case 'string':
                    return '';

                case 'int':
                    return 0;

                case 'bool':
                    return false;

                case 'float':
                    return 0.0;

                default:
                    if ($type->isBuiltin()) {
                        throw new \UnexpectedValueException(sprintf('The type "%s" is not supported by the generation of fake value. Please update the implementation.', $type->getName()));
                    }

                    \assert(class_exists($type->getName()) || interface_exists($type->getName()));

                    return $this->createStub($type->getName());
            }
        }

        return null;
    }
}
