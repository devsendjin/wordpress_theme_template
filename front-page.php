<?php

wp_enqueue_script('page-main', Utils::getAssetUrlWithTimestamp('/js/build/page-main.js'), [ 'bundle' ], null, true);
wp_enqueue_style('page-main', Utils::getAssetUrlWithTimestamp('/css/page-main.css'), [ 'bundle' ], null);

SiteConfig::setPageClasses('page-main');
get_header();

?>
    <main class="main">
        <section class="section section-default">
            <div class="container">
                <?php the_content() ?>
            </div>
        </section>
    </main>
<?php
get_footer();
