<?php

namespace Behat\Mink\Element;

class DocumentElement extends ActionableElement
{
    public function getXpath()
    {
        return '/html';
    }

    public function getContent()
    {
        return $this->getSession()->getDriver()->getContent();
    }

    public function getText()
    {
        $html = $this->find('xpath', $this->getXpath());

        if (null !== $html) {
            return $html->getText();
        }
    }
}
