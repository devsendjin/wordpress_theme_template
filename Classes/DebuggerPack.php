<?php

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use DebugBar\StandardDebugBar;

class DebuggerPack
{
    private static $whoops;
    public static $debugBar;
    public static $debugbarRenderer;

    public function whoopsDebuggerInit()
    {
        self::$whoops = new Run;
        self::$whoops->pushHandler(new PrettyPageHandler);
        self::$whoops->register();
    }

    public function debugBarInit()
    {
        self::$debugBar = new StandardDebugBar();
        self::$debugbarRenderer = self::$debugBar->getJavascriptRenderer();
        self::$debugbarRenderer->setBaseUrl(SiteConfig::$templateUri . self::$debugbarRenderer->getBaseUrl());
    }

}

Kint::$enabled_mode = SiteConfig::isDevMode();
if (SiteConfig::isDevMode()) {
    $customDebugger = new DebuggerPack();
    $customDebugger->whoopsDebuggerInit();
    $customDebugger->debugBarInit();
}
