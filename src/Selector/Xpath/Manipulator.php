<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Selector\Xpath;

/**
 * XPath manipulation utility.
 *
 * @author Graham Bates
 * @author Christophe Coevoet <stof@notk.org>
 */
class Manipulator
{
    /**
     * Regex to find union operators not inside brackets.
     */
    const UNION_PATTERN = '/\|(?![^\[]*\])/';

    /**
     * Prepends the XPath prefix to the given XPath.
     *
     * The returned XPath will match elements matching the XPath inside an element
     * matching the prefix.
     *
     * @param string $xpath
     * @param string $prefix
     *
     * @return string
     */
    public function prepend($xpath, $prefix)
    {
        $expressions = array();

        // If the xpath prefix contains a union we need to wrap it in parentheses.
        if (preg_match(self::UNION_PATTERN, $prefix)) {
            $prefix = '('.$prefix.')';
        }

        // Split any unions into individual expressions.
        foreach ($this->splitUnionParts($xpath) as $expression) {
            $expression = trim($expression);
            $parenthesis = '';

            // If the union is inside some braces, we need to preserve the opening braces and apply
            // the prefix only inside it.
            if (preg_match('/^[\(\s*]+/', $expression, $matches)) {
                $parenthesis = $matches[0];
                $expression = substr($expression, strlen($parenthesis));
            }

            // add prefix before element selector
            if (0 === strpos($expression, '/')) {
                $expression = $prefix.$expression;
            } else {
                $expression = $prefix.'/'.$expression;
            }
            $expressions[] = $parenthesis.$expression;
        }

        return implode(' | ', $expressions);
    }

    /**
     * Splits the XPath into parts that are separated by the union operator.
     *
     * @param string $xpath
     *
     * @return string[]
     */
    private function splitUnionParts($xpath)
    {
        // Split any unions into individual expressions. We need to iterate
        // through the string to correctly parse opening/closing quotes and
        // braces which is not possible with regular expressions.
        $unionParts = array();
        $singleQuoteOpen = false;
        $doubleQuoteOpen = false;
        $bracketOpen = 0;
        $lastUnion = 0;
        for ($i = 0; $i < strlen($xpath); $i++) {
            if ($xpath{$i} === "'" && $doubleQuoteOpen === false) {
                if ($singleQuoteOpen === true) {
                    $singleQuoteOpen = false;
                } else {
                    $singleQuoteOpen = true;
                }
            } else if ($xpath{$i} === '"' && $singleQuoteOpen === false) {
                if ($doubleQuoteOpen === true) {
                    $doubleQuoteOpen = false;
                } else {
                    $doubleQuoteOpen = true;
                }
            } else if ($singleQuoteOpen === false && $doubleQuoteOpen === false) {
                if ($xpath{$i} === '[') {
                    $bracketOpen++;
                } else if ($xpath{$i} === ']') {
                    $bracketOpen--;
                } else if ($xpath{$i} === '|' && $bracketOpen === 0) {
                    $unionParts[] = substr($xpath, $lastUnion, $i - $lastUnion);
                    $lastUnion = $i + 1;
                }
            }
        }
        $unionParts[] = substr($xpath, $lastUnion);
        return $unionParts;
    }

}
