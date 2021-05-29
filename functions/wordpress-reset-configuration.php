<?php

// убираем комментарии Yoast SEO
add_action('wp_head', function () {
    ob_start(function ($o) {
        return preg_replace('/^\n?<!--.*?[Y]oast.*?-->\n?$/mi', '', $o);
    });
}, ~PHP_INT_MAX);


remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');

// удаляем type="text/javascript"
function mdt_remove_type_attr($tag, $handle)
{
    return preg_replace("/type=['\"]text\/(javascript|css)['\"]/", '', $tag);
}
add_filter('script_loader_tag', 'mdt_remove_type_attr', 10, 2);

add_filter('the_content', 'wptexturize');
add_filter('the_content', 'convert_smilies');
add_filter('the_content', 'convert_chars');
add_filter('the_content', 'wpautop');
add_filter('the_content', 'shortcode_unautop');
add_filter('the_content', 'prepend_attachment');

/**
 * Disable the emoji's
 */
function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
    add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);
}
add_action('init', 'disable_emojis');

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, array( 'wpemoji' ));
    } else {
        return array();
    }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch($urls, $relation_type)
{
    if ('dns-prefetch' == $relation_type) {
        /** This filter is documented in wp-includes/formatting.php */
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

        $urls = array_diff($urls, array( $emoji_svg_url ));
    }

    return $urls;
}

/**
 * Remove Theme Customizer from admin menu
 */
//function remove_customize()
//{
//    $customize_url_arr = array();
//    $customize_url_arr[] = 'customize.php'; // 3.x
//    $customize_url = add_query_arg('return', urlencode(wp_unslash($_SERVER['REQUEST_URI'])), 'customize.php');
//    $customize_url_arr[] = $customize_url; // 4.0 & 4.1
//    if (current_theme_supports('custom-header') && current_user_can('customize')) {
//        $customize_url_arr[] = add_query_arg('autofocus[control]', 'header_image', $customize_url); // 4.1
//        $customize_url_arr[] = 'custom-header'; // 4.0
//    }
//    if (current_theme_supports('custom-background') && current_user_can('customize')) {
//        $customize_url_arr[] = add_query_arg('autofocus[control]', 'background_image', $customize_url); // 4.1
//        $customize_url_arr[] = 'custom-background'; // 4.0
//    }
//    foreach ($customize_url_arr as $customize_url) {
//        remove_submenu_page('themes.php', $customize_url);
//    }
//}
//add_action('admin_menu', 'remove_customize', 999);

//function filter_function_name_7817($components, $that)
//{
//    foreach ($components as $key => $component) {
//        unset($components[$key]);
//    }
//    return $components;
//}
//
//add_action('plugins_loaded', 'on_plugins_loaded');
//function on_plugins_loaded()
//{
//    add_filter('customize_loaded_components', 'filter_function_name_7817', 10, 2);
//}
