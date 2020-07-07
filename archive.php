<?php

SiteConfig::setPageId('default-archive');

get_header();

the_post();
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
