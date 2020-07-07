<?php

require_once 'vendor/autoload.php';
require_once 'theme-autoload.php';
require_once 'functions/dump.php';
require_once 'functions/post-types.php';
require_once 'functions/wordpress-reset-configuration.php';

add_theme_support('post-thumbnails');
add_theme_support('menus');

$siteConfig = new SiteConfig(true);

function enqueue_assets()
{
    if (!is_admin()) {
        global $wp_scripts;
        wp_deregister_style('bodhi-svgs-attachment');
        wp_dequeue_style('wp-block-library');

        //меняем старую версию jQuery на новую, которая собирается вместе с бандлом
        foreach ($wp_scripts->registered as $script_obj) {
            if (in_array('jquery', $script_obj->deps)) {
                $jquery_index = array_search('jquery', $script_obj->deps);
                $script_obj->deps[$jquery_index] = 'bundle';
            }
        }
    }
    wp_enqueue_style('bundle', Utils::getAssetUrlWithTimestamp('/css/bundle.css'), [], null);
//    wp_deregister_script('jquery');
//    wp_deregister_script('wp-embed');
    wp_enqueue_script('bundle', Utils::getAssetUrlWithTimestamp('/js/min/bundle.js'), [], null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_assets');

function theme_register_nav_menu()
{
    register_nav_menu('main-menu', 'Главное меню');
}

add_action('after_setup_theme', 'theme_register_nav_menu');

// обязательный для темы плагин kama thumbnail
// if (!is_admin() && !function_exists('kama_thumb_img')) {
//     wp_die('Активируйте обязательный для темы плагин Kama Thumbnail');
// }
