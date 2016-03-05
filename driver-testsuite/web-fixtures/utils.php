<?php

function html_escape_value($data)
{
    if (!is_array($data)) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8', false);
    }

    $escapedData = array();

    foreach ($data as $key => $value) {
        $escapedData[html_escape_value($key)] = html_escape_value($value);
    }

    return $escapedData;
}
