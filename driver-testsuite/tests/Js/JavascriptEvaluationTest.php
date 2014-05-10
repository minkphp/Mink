<?php

namespace Behat\Mink\Tests\Driver\Js;

use Behat\Mink\Tests\Driver\TestCase;

class JavascriptEvaluationTest extends TestCase
{
    /**
     * Tests, that `wait` method returns check result after exit.
     */
    public function testWaitReturnValue()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $found = $this->getSession()->wait(5000, '$("#draggable").length == 1');
        $this->assertTrue($found);
    }

    public function testWait()
    {
        $this->getSession()->visit($this->pathTo('/js_test.php'));

        $waitable = $this->getSession()->getPage()->findById('waitable');
        $this->assertNotNull($waitable);

        $waitable->click();
        $this->getSession()->wait(3000, '$("#waitable").has("div").length > 0');
        $this->assertEquals('arrived', $this->getSession()->getPage()->find('css', '#waitable > div')->getText());

        $waitable->click();
        $this->getSession()->wait(3000, 'false');
        $this->assertEquals('timeout', $this->getSession()->getPage()->find('css', '#waitable > div')->getText());
    }

    /**
     * @dataProvider provideExecutedScript
     */
    public function testExecuteScript($script)
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->getSession()->executeScript($script);

        sleep(1);

        $heading = $this->getSession()->getPage()->find('css', 'h1');
        $this->assertNotNull($heading);
        $this->assertEquals('Hello world', $heading->getText());
    }

    public function provideExecutedScript()
    {
        return array(
            array('document.querySelector("h1").textContent = "Hello world"'),
            array('document.querySelector("h1").textContent = "Hello world";'),
            array('function () {document.querySelector("h1").textContent = "Hello world";}()'),
            array('function () {document.querySelector("h1").textContent = "Hello world";}();'),
            array('(function () {document.querySelector("h1").textContent = "Hello world";})()'),
            array('(function () {document.querySelector("h1").textContent = "Hello world";})();'),
        );
    }

    /**
     * @dataProvider provideEvaluatedScript
     */
    public function testEvaluateJavascript($script)
    {
        $this->getSession()->visit($this->pathTo('/index.php'));

        $this->assertSame(2, $this->getSession()->evaluateScript($script));
    }

    public function provideEvaluatedScript()
    {
        return array(
            array('1 + 1'),
            array('1 + 1;'),
            array('return 1 + 1'),
            array('return 1 + 1;'),
            array('function () {return 1+1;}()'),
            array('(function () {return 1+1;})()'),
            array('return function () { return 1+1;}()'),
            array('return (function () {return 1+1;})()'),
        );
    }
}
