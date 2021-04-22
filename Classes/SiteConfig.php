<?php

class SiteConfig
{
  private static $isDevMode = false;

  private static $pageId = null; // значение устанавливается перед вызовом get_header(); в каждом шаблоне
  private static $pageClasses = null; // значение устанавливается перед вызовом get_header(); в каждом шаблоне

  public static $templateUri;
  public static $contactsData;
  public static $homeUrl;

  public function __construct()
  {
    self::$templateUri = get_template_directory_uri();
    self::$contactsData = get_option('contacts_data');
    self::$homeUrl = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
  }

  public static function isDevMode(): bool
  {
    return self::$isDevMode;
  }

  public static function enableDevMode(): bool
  {
    return self::$isDevMode = true;
  }

  public static function renderTagAttributes($attrs = []): string
  {
    $attributes = '';

    if (!empty($attrs)) {
      foreach ($attrs as $key => $value) {
        if (!empty($value)) {
          if (is_array($value)) {
            // filter falsy values
            $value = implode(' ', array_filter($value, static function ($item) {
              return !empty($item);
            }));
          }
          $attributes .= $key . '="' . $value . '" ';
        }
      }
      return trim($attributes);
    }

    return $attributes;
  }

  public static function getPageId(): ?string
  {
    return self::$pageId;
  }

  /**
   * @param string $pageId - id страницы
   */
  public static function setPageId(string $pageId): void
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
  public static function setPageClasses(string $pageClasses): void
  {
    self::$pageClasses = $pageClasses;
  }
}
