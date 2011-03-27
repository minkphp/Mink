<?php

namespace Behat\Mink\Exception;

class ElementNotFound extends Exception
{
    public function __construct($type = null, $locator = null, $code = 0, $previous = null)
    {
        if (null !== $type) {
            $message = $type . ' ';
        } else {
            $message = 'tag ';
        }

        if (null !== $locator) {
            $message .= 'with locator: "' . $locator . '" ';
        }

        $message .= 'not found';

        parent::__construct($message, $code, $previous);
    }
}
