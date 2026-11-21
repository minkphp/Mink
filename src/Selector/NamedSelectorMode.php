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
 * How the "named" selector resolves a locator.
 *
 * This is a placeholder for an enum, which the minimum PHP version does not allow yet.
 */
final class NamedSelectorMode
{
    /**
     * Try an exact match first, and fall back to a partial one when it finds nothing.
     */
    public const PARTIAL_FALLBACK = 'partial_fallback';

    /**
     * Only match exactly, the way the "named_exact" selector does.
     */
    public const EXACT = 'exact';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
