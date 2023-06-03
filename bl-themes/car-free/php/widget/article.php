<article id="webslides">
<?php Theme::plugins('siteBodyBegin'); ?>
	<?php
		if ($WHERE_AM_I == 'home') {
			include(THEME_DIR_PHP.'home.php');
		} else {
			include(THEME_DIR_PHP.'page.php');
		}
	?>
</article>