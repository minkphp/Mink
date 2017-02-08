<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink;

use Behat\Mink\Element\Element;
use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\TraversableElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementHtmlException;
use Behat\Mink\Exception\ElementTextException;
use Behat\Mink\Exception\ResponseTextException;

/**
 * Wraps the Mink WebAssert so that the asserts will retry in a loop.
 *
 * This is needed for JS based apps where we don't have the normal
 * browser 'user action' -> 'http request' -> 'page load' loop.
 *
 * To use this class you can add it to your context with...
 * <code>
 * class MyContext
 * extends Behat\MinkExtension\Context\(Raw)MinkContext
 * {
 *     public function assertSession($name = null)
 *     {
 *         return new RetryWebAssert($this->getMink()->getSession($name));  
 *     }
 * }
 * </code>
 *
 * It also exposes the assert* methods as public so that you can
 * use them in custom retry functions...
 * <code>
 * public function iCanSee($what)
 * {
 *     $assert = $this->assertSession();
 *     $assert->retry(
 *         function($what) use ($assert)
 *         {
 *             $el  = $assert->elementExists('css', $what);
 *             $msg = sprintf('The "%s" element was not found or visible.', $what);
 *             $assert->assertElement($element->isVisible(), $msg, $el);
 *         },
 *         func_get_args()
 *     );
 * }
 * </code>
 */
class RetryWebAssert
extends WebAssert
{

    const RETRY_SECONDS = 5.0;
    const RETRY_SLEEP   = 0.25;

    /**
     * Retry a function
     *
     * @param callable $fn
     * @param array $args Arguments to pass to the function.
     * @param $retrySeconds How long should we attempt to retry for.
     * @param $retrySleep The amount of time we usleep between retries.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function retry(
                        callable $fn,
                        array $args = array(),
                        $retrySeconds = self::RETRY_SECONDS,
                        $retrySleep = self::RETRY_SLEEP
                    )
    {
        $usleep   = ((float) $retrySleep) * 1000000;
        $giveUpAt = microtime(TRUE) + ((float) $retrySeconds);
        do
        {
            try
            {
                return call_user_func_array($fn, $args);
            }
            catch(ExpectationException $e)
            {
                usleep($usleep);
            }
            catch(\Exception $e)
            {
                throw $e;
            }
        }
        while(microtime(TRUE) < $giveUpAt);
        throw $e;
    }

    /**
     * Retry a call to a method on our parent class.
     *
     * @param $methodName
     * @param array $args
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function retryParent($methodName, array $args)
    {
        $class  = new ReflectionObject($this);
        $method = new ReflectionMethod($class->getParentClass()->getName(), $methodName);
        $assert = $this;
        return $this->retry(
                          function() use ($assert, $method)
                          {
                              return $method->invokeArgs($assert, func_get_args());
                          },
                          $args
                      );
    }

    /**
     * {@inheritdoc}
     */
    public function addressEquals($page)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function addressNotEquals($page)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function addressMatches($regex)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function cookieEquals($name, $value)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function cookieExists($name)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function statusCodeEquals($code)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function statusCodeNotEquals($code)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pageTextContains($text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pageTextNotContains($text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pageTextMatches($regex)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pageTextNotMatches($regex)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function responseContains($text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function responseNotContains($text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function responseMatches($regex)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function responseNotMatches($regex)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementsCount($selectorType, $selector, $count, ElementInterface $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementExists($selectorType, $selector, ElementInterface $container = NULL)
    {
        return $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementNotExists($selectorType, $selector, ElementInterface $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementTextContains($selectorType, $selector, $text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementTextNotContains($selectorType, $selector, $text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementContains($selectorType, $selector, $html)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementNotContains($selectorType, $selector, $html)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementAttributeExists($selectorType, $selector, $attribute)
    {
        return $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementAttributeContains($selectorType, $selector, $attribute, $text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function elementAttributeNotContains($selectorType, $selector, $attribute, $text)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function fieldExists($field, TraversableElement $container = NULL)
    {
        return $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function fieldNotExists($field, TraversableElement $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function fieldValueEquals($field, $value, TraversableElement $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function fieldValueNotEquals($field, $value, TraversableElement $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function checkboxChecked($field, TraversableElement $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function checkboxNotChecked($field, TraversableElement $container = NULL)
    {
        $this->retryParent(__FUNCTION__, func_get_args());
    }

    /**
     * Asserts a condition.
     *
     * @param bool   $condition
     * @param string $message   Failure message
     *
     * @throws ExpectationException when the condition is not fulfilled
     */
    public function assert($condition, $message)
    {
        if ($condition) {
            return;
        }

        throw new ExpectationException($message, $this->session);
    }

    /**
     * Asserts a condition involving the response text.
     *
     * @param bool   $condition
     * @param string $message   Failure message
     *
     * @throws ResponseTextException when the condition is not fulfilled
     */
    public function assertResponseText($condition, $message)
    {
        if ($condition) {
            return;
        }

        throw new ResponseTextException($message, $this->session);
    }

    /**
     * Asserts a condition on an element.
     *
     * @param bool    $condition
     * @param string  $message   Failure message
     * @param Element $element
     *
     * @throws ElementHtmlException when the condition is not fulfilled
     */
    public function assertElement($condition, $message, Element $element)
    {
        if ($condition) {
            return;
        }

        throw new ElementHtmlException($message, $this->session, $element);
    }

    /**
     * Asserts a condition involving the text of an element.
     *
     * @param bool    $condition
     * @param string  $message   Failure message
     * @param Element $element
     *
     * @throws ElementTextException when the condition is not fulfilled
     */
    public function assertElementText($condition, $message, Element $element)
    {
        if ($condition) {
            return;
        }

        throw new ElementTextException($message, $this->session, $element);
    }


} 