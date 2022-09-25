<?php

error_reporting(E_ALL | E_NOTICE);

set_error_handler(static function ($severity, $message, $file, $line): void {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

function d(...$args): void
{
    echo '<pre>';
    print_r($args);
    echo '</pre>';
}

function dd(...$args): void
{
    d(...$args);
    die;
}
