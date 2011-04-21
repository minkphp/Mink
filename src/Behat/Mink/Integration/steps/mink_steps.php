<?php

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Mink\Exception\ElementNotFoundException;

$steps->Given('/^(?:|I )am on (?P<page>.+)$/', function($world, $page) {
    $world->getSession()->visit($world->getPathTo($page));
});

$steps->When('/^(?:|I )go to (?P<page>.+)$/', function($world, $page) {
    $world->getSession()->visit($world->getPathTo($page));
});

$steps->When('/^(?:|I )press "(?P<button>[^"]*)"$/', function($world, $button) {
    $world->getSession()->getPage()->clickButton($button);
});

$steps->When('/^(?:|I )follow "(?P<link>[^"]*)"$/', function($world, $link) {
    $world->getSession()->getPage()->clickLink($link);
});

$steps->When('/^(?:|I )fill in "(?P<field>[^"]*)" with "(?P<value>[^"]*)"$/', function($world, $field, $value) {
    $world->getSession()->getPage()->fillField($field, $value);
});

$steps->When('/^(?:|I )fill in "(?P<value>[^"]*)" for "(?P<field>[^"]*)"$/', function($world, $field, $value) {
    $world->getSession()->getPage()->fillField($field, $value);
});

$steps->When('/^(?:|I )fill in the following:$/', function($world, $fieldsTable) {
    $page = $world->getSession()->getPage();
    foreach ($fieldsTable->getRowsHash() as $field => $value) {
        $page->fillField($field, $value);
    }
});

$steps->When('/^(?:|I )select "(?P<option>[^"]*)" from "(?P<select>[^"]*)"$/', function($world, $select, $option) {
    $world->getSession()->getPage()->selectFieldOption($select, $option);
});

$steps->When('/^(?:|I )check "(?P<option>[^"]*)"$/', function($world, $option) {
    $world->getSession()->getPage()->checkField($option);
});

$steps->When('/^(?:|I )uncheck "(?P<option>[^"]*)"$/', function($world, $option) {
    $world->getSession()->getPage()->uncheckField($option);
});

$steps->When('/^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>[^"]*)"$/', function($world, $field, $path) {
    $world->getSession()->getPage()->attachFileToField($field, $path);
});

$steps->Then('/^(?:|I )should see "(?P<text>[^"]*+)"$/', function($world, $text) {
    assertRegExp('/'.preg_quote($text).'/', $world->getSession()->getPage()->getContent());
});

$steps->Then('/^(?:|I )should not see "(?P<text>[^"]*+)"$/', function($world, $text) {
    assertNotRegExp('/'.preg_quote($text).'/', $world->getSession()->getPage()->getContent());
});

$steps->Then('/^the "(?P<field>[^"]*)" field should contain "(?P<value>[^"]*)"$/', function($world, $field, $value) {
    $node = $world->getSession()->getPage()->findField($field);

    if (null === $node) {
        throw new ElementNotFoundException('field', $field);
    }

    assertContains($value, $node->getValue());
});

$steps->Then('/^the "(?P<field>[^"]*)" field should not contain "(?P<value>[^"]*)"$/', function($world, $field, $value) {
    $node = $world->getSession()->getPage()->findField($field);

    if (null === $field) {
        throw new ElementNotFoundException('field', $field);
    }

    assertNotContains($value, $node->getValue());
});

$steps->Then('/^the "(?P<checkbox>[^"]*)" checkbox should be checked$/', function($world, $checkbox) {
    $field = $world->getSession()->getPage()->findField($checkbox);

    if (null === $field) {
        throw new ElementNotFoundException('field', $checkbox);
    }

    assertTrue($field->isChecked());
});

$steps->Then('/^the "(?P<checkbox>[^"]*)" checkbox should not be checked$/', function($world, $checkbox) {
    $field = $world->getSession()->getPage()->findField($checkbox);

    if (null === $field) {
        throw new ElementNotFoundException('field', $checkbox);
    }

    assertFalse($field->isChecked());
});

$steps->Then('/^(?:|I )should be on (?P<page>.+)$/', function($world, $page) {
    assertEquals(
        parse_url($world->getPathTo($page), PHP_URL_PATH),
        parse_url($world->getSession()->getCurrentUrl(), PHP_URL_PATH)
    );
});

$steps->Then('/^the "(?P<element>[^"]*)" element should contain "(?P<value>[^"]*)"$/', function($world, $element, $value) {
    $node = $world->getSession()->getPage()->find('xpath', $element);

    if (null === $node) {
        throw new ElementNotFoundException('element', $element);
    }

    assertContains($value, preg_replace('/\s+/', ' ', str_replace("\n", '', $node->getText())));
});

$steps->Then('/^(?:|I )should see "(?P<element>[^"]*)" element$/', function($world, $element) {
    $node = $world->getSession()->getPage()->find('xpath', $element);

    if (null === $node) {
        throw new ElementNotFoundException('element', $element);
    }

    assertNotNull($node);
});

$steps->Then('/^(?:|I )should not see "(?P<element>[^"]*)" element$/', function($world, $element) {
    assertNull($world->getSession()->getPage()->find('xpath', $element));
});

$steps->Then('/^the "(?P<element>[^"]*)" element should link to (?P<href>.*)$/', function($world, $element, $href) {
    $node = $world->getSession()->getPage()->find('xpath', $element);

    if (null === $node) {
        throw new ElementNotFoundException('element', $element);
    }

    $href_parts = parse_url($href);
    $href = array_merge(
        parse_url($world->getParameter('start_url')),
        $href_parts
    );

    assertSame($href['scheme'].'://'.$href['host'].$href['path'], $node->getAttribute('href'));
});

$steps->Then('/^the "(?P<element>[^"]*)" element should have a "(?P<attribute>[a-zA-Z\-\_]*)" attribute of "(?P<value>[^"]*)"$/', function($world, $element, $attribute, $value) {
    $node = $world->getSession()->getPage()->find('xpath', $element);

    if (null === $node) {
        throw new ElementNotFoundException('element', $element);
    }

    assertSame($value, $node->getAttribute($attribute));
});

$steps->Then('/the response status code should be (?P<code>\d+)/', function($world, $code) {
    assertSame($world->getSession()->getStatusCode(), (int) $code);
});
