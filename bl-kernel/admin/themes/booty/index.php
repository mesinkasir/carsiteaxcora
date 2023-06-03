<!DOCTYPE html>
<html>
<head>
	<title>OTO RENT CAR CMS <?php echo $layout['title'] ?></title>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex,nofollow">
<?php echo Theme::favicon('img/logo.webp'); ?>
	<?php
		echo Theme::cssBootstrap();
		echo Theme::cssLineAwesome();
		echo Theme::css(array(
			'bludit.css',
			'bludit.bootstrap.css'
		), DOMAIN_ADMIN_THEME_CSS);
		echo Theme::css(array(
			'jquery.datetimepicker.min.css',
			'select2.min.css',
			'select2-bootstrap4.min.css'
		), DOMAIN_CORE_CSS);
	?>

	<?php
		echo Theme::jquery();
		echo Theme::jsBootstrap();
		echo Theme::jsSortable();
		echo Theme::js(array(
			'jquery.datetimepicker.full.min.js',
			'select2.full.min.js',
			'functions.js'
		), DOMAIN_CORE_JS, null);
	?>

	<?php Theme::plugins('adminHead') ?>
</head>
<body class="h-100">

<!-- Plugins -->
<?php Theme::plugins('adminBodyBegin') ?>

<!-- Javascript dynamic generated by PHP -->
<?php
	echo '<script charset="utf-8">'.PHP_EOL;
	include(PATH_CORE_JS.'variables.php');
	echo '</script>'.PHP_EOL;

	echo '<script charset="utf-8">'.PHP_EOL;
	include(PATH_CORE_JS.'bludit-ajax.php');
	echo '</script>'.PHP_EOL;
?>

<!-- Overlay background -->
<div id="jsshadow"></div>

<!-- Alert -->
<?php include('html/alert.php'); ?>

<!-- Navbar, only for small devices -->
<?php include('html/navbar.php'); ?>

<div class="container h-100">
	<!-- 25%/75% split on large devices, small, medium devices hide -->
	<div class="row h-100">

		<!-- LEFT SIDEBAR - Display only on large devices -->
		<div class="sidebar col-lg-2 d-none d-lg-block">
		<?php include('html/sidebar.php'); ?>
		</div>

		<!-- RIGHT MAIN -->
		<div class="col-lg-10 pt-3 pb-1 h-100">
		<?php
			if (Sanitize::pathFile(PATH_ADMIN_VIEWS, $layout['view'].'.php')) {
				include(PATH_ADMIN_VIEWS.$layout['view'].'.php');
			} elseif ($layout['plugin'] && method_exists($layout['plugin'], 'adminView')) {
				echo $layout['plugin']->adminView();
			} else {
				echo '<h1 class="text-center">'.$L->g('Page not found').'</h1>';
				echo '<h2 class="text-center">'.$L->g('Choose a page from the sidebar.').'</h2>';
			}
		?>
		</div>
	</div>
</div>

<!-- Plugins -->
<?php Theme::plugins('adminBodyEnd') ?>

</body>
</html>
