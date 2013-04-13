<?php

namespace Behat\Mink\Driver;

/**
 * Popup handling interface for driver
 */
interface PopupHandlingInterface
{
    /**
     * Returns text of opened popup
     *
     */
    public function getPopupText();

    /**
     * Fills in text into popup
     *
     * @param string $text
     */
    public function setPopupText($text);

    /**
     * Accept opened popup
     *
     */
    public function acceptPopup();

    /**
     * Dismisses opened popup
     *
     */
    public function dismissPopup();
}
