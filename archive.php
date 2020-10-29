<?php

SiteConfig::setPageClasses('default-archive');

get_header();

the_post();
?>
<main class="main">
    <section class="section-default">
        <div class="container">
            <?php the_content(); ?>
        </div>
    </section>
</main>

<?php
get_footer();
