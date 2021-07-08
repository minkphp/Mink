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
class PartialNamedIncludingHiddenSelector extends PartialNamedSelector
{
    public function __construct()
    {
        $this->registerReplacement('%notFieldTypeFilter%', "not(%buttonTypeFilter%)");

        parent::__construct();
    }
}
