<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Selector\Xpath;

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
            $prefix = '(' . $prefix . ')';
        }

        // Split any unions into individual expressions.
        foreach (preg_split(self::UNION_PATTERN, $xpath) as $expression) {
            $expression = trim($expression);
            // add prefix before element selector
            if (0 === strpos($expression, '/')) {
                $expression = $prefix . $expression;
            } else {
                $expression = $prefix . '/' . $expression;
            }
            $expressions[] = $expression;
        }

        return implode(' | ', $expressions);
    }
}
