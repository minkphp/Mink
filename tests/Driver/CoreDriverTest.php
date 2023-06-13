<?php

namespace Behat\Mink\Tests\Driver;

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

        $driver->expects($this->once())
            ->method('findElementXpaths')
            ->with('xpath')
            ->willReturn(array('xpath1', 'xpath2'));

        $elements = $driver->find('xpath');

        $this->assertIsArray($elements);

        $this->assertCount(2, $elements);

        $this->assertSame('xpath1', $elements[0]);
        $this->assertSame('xpath2', $elements[1]);
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

        if ($argument->getClass()) {
            return $this->getMockBuilder($argument->getClass()->getName())
                ->disableOriginalConstructor()
                ->getMock();
        }

        return null;
    }
}
