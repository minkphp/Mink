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
 * Named selectors engine. Uses registered XPath selectors to create new expressions.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PartialNamedSelector extends NamedSelector
{
    protected function getRawReplacements()
    {
        return array_merge(parent::getRawReplacements(), [
            '%tagTextMatch%' => 'contains(normalize-space(string(.)), %locator%)',
            '%valueMatch%' => 'contains(./@value, %locator%)',
            '%titleMatch%' => 'contains(./@title, %locator%)',
            '%altMatch%' => 'contains(./@alt, %locator%)',
            '%relMatch%' => 'contains(./@rel, %locator%)',
            '%labelAttributeMatch%' => 'contains(./@label, %locator%)',
        ]);
    }
}
