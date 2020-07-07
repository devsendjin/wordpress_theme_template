<?php

global $wp;

$requestedPage = array_reverse(explode('/', $wp->request))[0];

if($requestedPage !== '404'){
    header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
    header('Location: '.SiteConfig::$homeUrl.'404/');
    exit(0);
}

header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

SiteConfig::setPageId('page-404');
SiteConfig::setPageClasses('page-404');

wp_enqueue_style('page-404', Utils::getAssetUrlWithTimestamp('/css/page-404.css'), ['bundle'], null);

get_header();
?>
<main class="main">
    <section class="block block-not-found">
        <div class="content block-content">
            <h1 class="block-title">Page not found :(</h1>
            <p class="description">The page you are looking for doesn’t exist or another arror occured.</p>
            <a href="<?= SiteConfig::$homeUrl; ?>" class="btn">Back to homepage</a>
        </div>
    </section>
</main>
<?php get_footer(); ?>
