<?php

SiteConfig::setPageId('page-default');

get_header();

the_post();
?>
<main class="main page">
    <section class="block">
        <div class="content">
            <?php the_content(); ?>
        </div>
    </section>
</main>

<?php
get_footer();
