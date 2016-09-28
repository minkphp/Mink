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
     * Pipe ('|') inside xpath strings placeholder.
     */
    const PIPE_PLACEHOLDER = '****PIPE-CHAR-PLACEHOLDER****';

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

        // If the pipe ('|') character is present in xpath strings, this will break the later split into individual
        // expressions. Replacing all pipe characters with a placeholder will preserve them during split.
        if (preg_match_all('@(["\'])(?:\\\1|.)*?\1@', $xpath, $matches)) {
            $replacements = array();
            foreach ($matches[0] as $string) {
                // Create a list of replacements.
                if (false !== strpos($string, '|') && !isset($replacements[$string])) {
                    $replacements[$string] = str_replace('|', self::PIPE_PLACEHOLDER, $string);
                }
            }
            // Replace pipe '|' character with a placeholder inside all strings.
            if ($replacements) {
                $xpath = strtr($xpath, $replacements);
            }
        }

        // Split any unions into individual expressions.
        foreach (preg_split(self::UNION_PATTERN, $xpath) as $expression) {
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

        return str_replace(self::PIPE_PLACEHOLDER, '|', implode(' | ', $expressions));
    }
}
