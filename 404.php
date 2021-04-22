<?php

/*
Template Name: 404
Template Post Type: page
*/

global $wp;

$requestedPage = array_reverse( explode( '/', $wp->request ) )[0];

if ( $requestedPage !== '404' ) {
  header( $_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently' );
  header( 'Location: ' . SiteConfig::$homeUrl . '404/' );
  exit( 0 );
}

header( $_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found' );

SiteConfig::setPageClasses( 'page-404' );

wp_enqueue_style( 'page-404', Utils::getAssetUrlWithTimestamp( '/css/page-404.css' ), [ 'bundle' ], null );

get_header();
?>
<main class="main">
  <section class="section-not-found">
    <div class="container">
      <h1 class="section-title">Page not found :(</h1>
      <p class="section-not-found__description">The page you are looking for doesn’t exist or another arror occured.</p>
      <a href="<?= SiteConfig::$homeUrl ?>" class="button">Back to homepage</a>
    </div>
  </section>
</main>
<?php get_footer(); ?>
