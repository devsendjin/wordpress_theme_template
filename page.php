<?php

SiteConfig::setPageClasses( 'page-default' );

get_header();

the_post();
?>
    <main class="main">
        <section class="section">
            <div class="container">
                <?php the_content(); ?>
            </div>
        </section>
    </main>

<?php
get_footer();
