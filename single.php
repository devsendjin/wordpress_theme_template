<?php

SiteConfig::setPageClasses('single-post');

get_header();
?>
<main class="main">
	<div class="section">
		<div class="container">
        <?php the_content(); ?>
		</div>
	</div>
</main>
<?php
get_footer();
