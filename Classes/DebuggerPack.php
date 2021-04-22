<?php

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

Kint::$enabled_mode = SiteConfig::isDevMode();
if (SiteConfig::isDevMode()) {
    $whoops = new Run;
    $whoops->pushHandler( new PrettyPageHandler );
    $whoops->register();
}
