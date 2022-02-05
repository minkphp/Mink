<?php

/*
 * This file is part of the Mink package.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Selector;

/**
 * Exact match selector engine. Like the Named selector engine but ignores partial matches.
 */
class ExactNamedSelector extends NamedSelector
{
    protected function getRawReplacements()
    {
        return array_merge(parent::getRawReplacements(), [
            '%tagTextMatch%' => 'normalize-space(string(.)) = %locator%',
            '%valueMatch%' => './@value = %locator%',
            '%titleMatch%' => './@title = %locator%',
            '%altMatch%' => './@alt = %locator%',
            '%relMatch%' => './@rel = %locator%',
            '%labelAttributeMatch%' => './@label = %locator%',
        ]);
    }
}
