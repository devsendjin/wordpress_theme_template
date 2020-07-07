<?php

global $siteConfig;

wp_enqueue_script('front-page', Utils::getAssetUrlWithTimestamp('/js/min/front-page.js'), ['bundle'], null, true);
wp_enqueue_style('front-page', Utils::getAssetUrlWithTimestamp('/css/front-page.css'), ['bundle'], null);

SiteConfig::setPageId('front-page');
get_header();

// $metaData = Utils::getNormalizedMetaData();

?>
<main class="main">
    <section class="block block-default">
        <div class="content">
            <?php the_content(); ?>
        </div>
    </section>
</main>
<?php
get_footer();
