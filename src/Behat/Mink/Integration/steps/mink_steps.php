<?php

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Mink\Exception\ElementNotFoundException;

$steps->Given('/^(?:|I )am on (.+)$/', function($world, $page) {
    $world->getSession()->visit($world->getPathTo($page));
});

$steps->When('/^(?:|I )go to (.+)$/', function($world, $page) {
    $world->getSession()->visit($world->getPathTo($page));
});

$steps->When('/^(?:|I )press "([^"]*)"$/', function($world, $button) {
    $world->getSession()->getPage()->clickButton($button);
});

$steps->When('/^(?:|I )follow "([^"]*)"$/', function($world, $link) {
    $world->getSession()->getPage()->clickLink($link);
});

$steps->When('/^(?:|I )fill in "([^"]*)" with "([^"]*)"$/', function($world, $field, $value) {
    $world->getSession()->getPage()->fillField($field, $value);
});

$steps->When('/^(?:|I )fill in "([^"]*)" for "([^"]*)"$/', function($world, $value, $field) {
    $world->getSession()->getPage()->fillField($field, $value);
});

$steps->When('/^(?:|I )fill in the following:$/', function($world, $fieldsTable) {
    $page = $world->getSession()->getPage();
    foreach ($fieldsTable->getRowsHash() as $field => $value) {
        $page->fillField($field, $value);
    }
});

$steps->When('/^(?:|I )select "([^"]*)" from "([^"]*)"$/', function($world, $value, $field) {
    $world->getSession()->getPage()->selectFieldOption($field, $value);
});

$steps->When('/^(?:|I )check "([^"]*)"$/', function($world, $field) {
    $world->getSession()->getPage()->checkField($field);
});

$steps->When('/^(?:|I )uncheck "([^"]*)"$/', function($world, $field) {
    $world->getSession()->getPage()->uncheckField($field);
});

$steps->When('/^(?:|I )attach the file "([^"]*)" to "([^"]*)"$/', function($world, $path, $field) {
    $world->getSession()->getPage()->attachFileToField($field, $path);
});

$steps->Then('/^(?:|I )should see "([^"]*)"$/', function($world, $text) {
    assertTrue($world->getSession()->getPage()->hasContent($text));
});

$steps->Then('/^(?:|I )should not see "([^"]*)"$/', function($world, $text) {
    assertFalse($world->getSession()->getPage()->hasContent($text));
});

$steps->Then('/^the "([^"]*)" field should contain "([^"]*)"$/', function($world, $field, $value) {
    $node = $world->getSession()->getPage()->findField($field);

    if (null === $node) {
        throw new ElementNotFoundException('field', $field);
    }

    assertContains($value, $node->getValue());
});

$steps->Then('/^the "([^"]*)" field should not contain "([^"]*)"$/', function($world, $name, $value) {
    $field = $world->getSession()->getPage()->findField($name);

    if (null === $field) {
        throw new ElementNotFoundException('field', $name);
    }

    assertNotContains($value, $field->getValue());
});

$steps->Then('/^the "([^"]*)" checkbox should be checked$/', function($world, $label) {
    $field = $world->getSession()->getPage()->findField($label);

    if (null === $field) {
        throw new ElementNotFoundException('field', $name);
    }

    assertTrue($field->isChecked());
});

$steps->Then('/^the "([^"]*)" checkbox should not be checked$/', function($world, $label) {
    $field = $world->getSession()->getPage()->findField($label);

    if (null === $field) {
        throw new ElementNotFoundException('field', $name);
    }

    assertFalse($field->isChecked());
});

$steps->Then('/^(?:|I )should be on (.+)$/', function($world, $page) {
    assertEquals(
        parse_url($world->getPathTo($page), PHP_URL_PATH),
        parse_url($world->getSession()->getCurrentUrl(), PHP_URL_PATH)
    );
});
