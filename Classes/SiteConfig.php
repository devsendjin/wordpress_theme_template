<?php

class SiteConfig
{
    private static $isDevMode;

    private static $pageId = null; // значение устанавливается перед вызовом get_header(); в каждом шаблоне
    private static $pageClasses = null; // значение устанавливается перед вызовом get_header(); в каждом шаблоне
    private static $unitDimension;

    public static $templateUri;
    public static $contactsData;
    public static $homeUrl;

    /**
     * SiteConfig constructor.
     * @param bool $isDevMode
     */
    public function __construct(bool $isDevMode = false)
    {
        self::$isDevMode = $isDevMode;
        self::$templateUri = get_template_directory_uri();
        self::$contactsData = get_option('contacts_data');
        self::$homeUrl = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
    }

    public static function isDevMode(): bool
    {
        return self::$isDevMode;
    }

    public static function getPageId(): ?string
    {
        return self::$pageId;
    }

    /**
     * @param string $pageId - id страницы
     */
    public static function setPageId($pageId): void
    {
        self::$pageId = $pageId;
    }

    public static function getPageClasses(): ?string
    {
        return self::$pageClasses;
    }

	/**
	 * @param string $pageClasses - названия css классов через пробел
	 */
	public static function setPageClasses($pageClasses): void
    {
        self::$pageClasses = $pageClasses;
    }

}
