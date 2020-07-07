<?php

SiteConfig::setPageId('single-post');

get_header();
?>
<main class="main">
	<div class="block">
		<div class="content">
        <?php the_content(); ?>
		</div>
	</div>
</main>
<?php
get_footer();
