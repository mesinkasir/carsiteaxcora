<?php

/*
 * Bludit
 * https://www.bludit.com
 * Author Diego Najar
 * Bludit is opensource software licensed under the MIT license.
*/

// Check PHP version
if (version_compare(phpversion(), '5.6', '<')) {
	$errorText = 'Current PHP version '.phpversion().', you need > 5.6.';
	error_log('[ERROR] '.$errorText, 0);
	exit($errorText);
}

// Check PHP modules
$modulesRequired = array('mbstring', 'json', 'gd', 'dom');
$modulesRequiredExit = false;
$modulesRequiredMissing = '';
foreach ($modulesRequired as $module) {
	if (!extension_loaded($module)) {
		$errorText = 'PHP module <b>'.$module.'</b> is not installed.';
		error_log('[ERROR] '.$errorText, 0);

		$modulesRequiredExit = true;
		$modulesRequiredMissing .= $errorText.PHP_EOL;
	}
}
if ($modulesRequiredExit) {
	echo 'PHP modules missing:';
	echo $modulesRequiredMissing;
	echo '';
	echo 'Error on installation , you need follow installation requipments';
#	echo '<a href="https://docs.bludit.com/en/getting-started/requirements">Please read Bludit requirements</a>.';
	exit(0);
}

// Security constant
define('BLUDIT', true);

// Directory separator
define('DS', DIRECTORY_SEPARATOR);

// PHP paths
define('PATH_ROOT',		__DIR__.DS);
define('PATH_CONTENT',		PATH_ROOT.'bl-content'.DS);
define('PATH_KERNEL',		PATH_ROOT.'bl-kernel'.DS);
define('PATH_LANGUAGES',	PATH_ROOT.'bl-languages'.DS);
define('PATH_UPLOADS',		PATH_CONTENT.'uploads'.DS);
define('PATH_TMP',		PATH_CONTENT.'tmp'.DS);
define('PATH_PAGES',		PATH_CONTENT.'pages'.DS);
define('PATH_WORKSPACES',	PATH_CONTENT.'workspaces'.DS);
define('PATH_DATABASES',	PATH_CONTENT.'databases'.DS);
define('PATH_PLUGINS_DATABASES',PATH_CONTENT.'databases'.DS.'plugins'.DS);
define('PATH_UPLOADS_PROFILES',	PATH_UPLOADS.'profiles'.DS);
define('PATH_UPLOADS_THUMBNAILS',PATH_UPLOADS.'thumbnails'.DS);
define('PATH_UPLOADS_PAGES',	PATH_UPLOADS.'pages'.DS);
define('PATH_HELPERS',		PATH_KERNEL.'helpers'.DS);
define('PATH_ABSTRACT',		PATH_KERNEL.'abstract'.DS);

// Protecting against Symlink attacks
define('CHECK_SYMBOLIC_LINKS', TRUE);

// Filename for pages
define('FILENAME', 'index.txt');

// Domain and protocol
define('DOMAIN', $_SERVER['HTTP_HOST']);

if (!empty($_SERVER['HTTPS'])) {
	define('PROTOCOL', 'https://');
} else {
	define('PROTOCOL', 'http://');
}

// Base URL
// Change the base URL or leave it empty if you want to Bludit try to detect the base URL.
$base = '';

if (!empty($_SERVER['DOCUMENT_ROOT']) && !empty($_SERVER['SCRIPT_NAME']) && empty($base)) {
	$base = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_NAME']);
	$base = dirname($base);
} elseif (empty($base)) {
	$base = empty( $_SERVER['SCRIPT_NAME'] ) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$base = dirname($base);
}

if (strpos($_SERVER['REQUEST_URI'], $base)!==0) {
	$base = '/';
} elseif ($base!=DS) {
	$base = trim($base, '/');
	$base = '/'.$base.'/';
} else {
	// Workaround for Windows Web Servers
	$base = '/';
}

define('HTML_PATH_ROOT', $base);

// Log separator
define('LOG_SEP', ' | ');

// JSON
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 128);
}

// Database format date
define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

// Charset, default UTF-8.
define('CHARSET', 'UTF-8');

// Default language file
define('DEFAULT_LANGUAGE_FILE', 'en.json');

// Set internal character encoding
mb_internal_encoding(CHARSET);

// Set HTTP output character encoding
mb_http_output(CHARSET);

// Directory permissions
define('DIR_PERMISSIONS', 0755);

// --- PHP Classes ---
include(PATH_ABSTRACT.'dbjson.class.php');
include(PATH_HELPERS.'sanitize.class.php');
include(PATH_HELPERS.'valid.class.php');
include(PATH_HELPERS.'text.class.php');
include(PATH_HELPERS.'log.class.php');
include(PATH_HELPERS.'date.class.php');
include(PATH_KERNEL.'language.class.php');

// --- LANGUAGE and LOCALE ---
// Try to detect the language from browser or headers
$languageFromHTTP = 'en';
$localeFromHTTP = 'en_US';

if (isset($_GET['language'])) {
	$languageFromHTTP = Sanitize::html($_GET['language']);
} else {
	// Try to detect the language browser
	$languageFromHTTP = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

	// Try to detect the locale
	if (function_exists('locale_accept_from_http')) {
		$localeFromHTTP = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}
}

$finalLanguage = 'en';
$languageFiles = getLanguageList();
foreach ($languageFiles as $fname=>$native) {
	if ( ($languageFromHTTP==$fname) || ($localeFromHTTP==$fname) ) {
		$finalLanguage = $fname;
	}
}

$L = $language = new Language($finalLanguage);

// Set locale
setlocale(LC_ALL, $localeFromHTTP);

// --- TIMEZONE ---

// Check if timezone is defined in php.ini
$iniDate = ini_get('date.timezone');
if (empty($iniDate)) {
	// Timezone not defined in php.ini, then set UTC as default.
	date_default_timezone_set('UTC');
}

// ============================================================================
// FUNCTIONS
// ============================================================================

// Returns an array with all languages
function getLanguageList() {
	$files = glob(PATH_LANGUAGES.'*.json');
	$tmp = array();
	foreach ($files as $file) {
		$t = new dbJSON($file, false);
		$native = $t->db['language-data']['native'];
		$locale = basename($file, '.json');
		$tmp[$locale] = $native;
	}

	return $tmp;
}

// Check if Bludit is installed
function alreadyInstalled() {
	return file_exists(PATH_DATABASES.'site.php');
}

// Check write permissions and .htaccess file
function checkSystem()
{
	$output = array();

	// Try to create .htaccess
	$htaccessContent = 'AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>

# Enable rewrite rules
RewriteEngine on

# Base directory
RewriteBase '.HTML_PATH_ROOT.'

# Deny direct access to the next directories
RewriteRule ^bl-content/(databases|workspaces|pages|tmp)/.*$ - [R=404,L]

# All URL process by index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*) index.php [PT,L]

</IfModule>';

	if (!file_put_contents(PATH_ROOT.'.htaccess', $htaccessContent)) {
		if (!empty($_SERVER['SERVER_SOFTWARE'])) {
			$webserver = Text::lowercase($_SERVER['SERVER_SOFTWARE']);
			if (Text::stringContains($webserver, 'apache') || Text::stringContains($webserver, 'litespeed')) {
				$errorText = 'Missing file, upload the file .htaccess';
				error_log('[ERROR] '.$errorText, 0);
				array_push($output, $errorText);
			}
		}
	}

	// Check mod_rewrite module
	if (function_exists('apache_get_modules') ) {
		if (!in_array('mod_rewrite', apache_get_modules())) {
			$errorText = 'Module mod_rewrite is not installed or loaded.';
			error_log('[ERROR] '.$errorText, 0);
			array_push($output, $errorText);
		}
	}

	// Try to create the directory content
	@mkdir(PATH_CONTENT, DIR_PERMISSIONS, true);

	// Check if the directory content is writeable.
	if (!is_writable(PATH_CONTENT)) {
		$errorText = 'Writing test failure, check directory "bl-content" permissions.';
		error_log('[ERROR] '.$errorText, 0);
		array_push($output, $errorText);
	}

	return $output;
}

// Install Bludit
function install($adminPassword, $timezone)
{
	global $L;

	if (!date_default_timezone_set($timezone)) {
		date_default_timezone_set('UTC');
	}

	$currentDate = Date::current(DB_DATE_FORMAT);

	// ============================================================================
	// Create directories
	// ============================================================================

	// Directories for initial pages
	$pagesToInstall = array('example-page-1-slug', 'example-page-2-slug',  'example-page-4-slug');
	foreach ($pagesToInstall as $page) {
		if (!mkdir(PATH_PAGES.$L->get($page), DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>'.PATH_PAGES.$L->get($page);
			error_log('[ERROR] '.$errorText, 0);
		}
	}

	// Directories for initial plugins
	$pluginsToInstall = array('tinymce', 'about', 'simple-stats', 'robots', 'canonical', 'opengraph', 'twitter-cards');
	foreach ($pluginsToInstall as $plugin) {
		if (!mkdir(PATH_PLUGINS_DATABASES.$plugin, DIR_PERMISSIONS, true)) {
			$errorText = 'Error when trying to created the directory=>'.PATH_PLUGINS_DATABASES.$plugin;
			error_log('[ERROR] '.$errorText, 0);
		}
	}

	// Directories for upload files
	if (!mkdir(PATH_UPLOADS_PROFILES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS_PROFILES;
		error_log('[ERROR] '.$errorText, 0);
	}

	if (!mkdir(PATH_UPLOADS_THUMBNAILS, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS_THUMBNAILS;
		error_log('[ERROR] '.$errorText, 0);
	}

	if (!mkdir(PATH_TMP, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_TMP;
		error_log('[ERROR] '.$errorText, 0);
	}

	if (!mkdir(PATH_WORKSPACES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_WORKSPACES;
		error_log('[ERROR] '.$errorText, 0);
	}

	if (!mkdir(PATH_UPLOADS_PAGES, DIR_PERMISSIONS, true)) {
		$errorText = 'Error when trying to created the directory=>'.PATH_UPLOADS_PAGES;
		error_log('[ERROR] '.$errorText, 0);
	}

	// ============================================================================
	// Create files
	// ============================================================================

	$dataHead = "<?php defined('BLUDIT') or die('Bludit CMS.'); ?>".PHP_EOL;

	$data = array();
	$slugs = array();
	$nextDate = $currentDate;
	foreach ($pagesToInstall as $page) {

		$slug = $page;
		$title = Text::replace('slug','title', $slug);
		$content = Text::replace('slug','content', $slug);
		$nextDate = Date::offset($nextDate, DB_DATE_FORMAT, '-1 minute');

		$data[$L->get($slug)]= array(
			'title'=>$L->get($title),
			'description'=>'',
			'username'=>'admin',
			'tags'=>array(),
			'type'=>(($slug=='example-page-4-slug')?'static':'published'),
			'date'=>$nextDate,
			'dateModified'=>'',
			'allowComments'=>true,
			'position'=>1,
			'coverImage'=>'',
			'md5file'=>'',
			'category'=>'general',
			'uuid'=>md5(uniqid()),
			'parent'=>'',
			'template'=>'',
			'noindex'=>false,
			'nofollow'=>false,
			'noarchive'=>false
		);

		array_push($slugs, $slug);

		file_put_contents(PATH_PAGES.$L->get($slug).DS.FILENAME, $L->get($content), LOCK_EX);
	}
	file_put_contents(PATH_DATABASES.'pages.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File site.php

	// If Bludit is not installed inside a folder, the URL doesn't need finish with /
	// Example (root): https://domain.com
	// Example (inside a folder): https://domain.com/folder/
	if (HTML_PATH_ROOT=='/') {
		$siteUrl = PROTOCOL.DOMAIN;
	} else {
		$siteUrl = PROTOCOL.DOMAIN.HTML_PATH_ROOT;
	}
	$data = array(
		'title'=>'RENT CAR CMS',
		'slogan'=>$L->get('👋 Welcome to rent car cms'),
		'description'=>$L->get('Congratulations you have successfully installed your rent car cms🚀'),
		'footer'=>'Copyright © '.Date::current('Y'),
		'itemsPerPage'=>3,
		'language'=>$L->currentLanguage(),
		'locale'=>$L->locale(),
		'timezone'=>$timezone,
		'theme'=>'car-free',
		'adminTheme'=>'booty',
		'homepage'=>'',
		'pageNotFound'=>'',
		'uriPage'=>'/',
		'uriTag'=>'/tag/',
		'uriCategory'=>'/category/',
		'uriBlog'=>'',
		'url'=>$siteUrl,
		'emailFrom'=>'no-reply@'.DOMAIN,
		'orderBy'=>'date',
		'currentBuild'=>'0',
		'twitter'=>'https://www.axcora.com',
		'facebook'=>'https://axcora.com',
		'codepen'=>'',
		'github'=> 'https://mesinkasir.github.io',
		'instagram'=>'',
		'gitlab'=>'',
		'linkedin'=>'',
		'xing'=>'',
		'dateFormat'=>'F j, Y',
		'extremeFriendly'=>true,
		'autosaveInterval'=>2,
		'titleFormatHomepage'=>'{{site-slogan}} | {{site-title}}',
		'titleFormatPages'=>'{{page-title}} | {{site-title}}',
		'titleFormatCategory'=>'{{category-name}} | {{site-title}}',
		'titleFormatTag'=>'{{tag-name}} | {{site-title}}',
		'imageRestrict'=>true,
		'imageRelativeToAbsolute'=>false
	);
	file_put_contents(PATH_DATABASES.'site.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File users.php
	$salt = uniqid();
	$passwordHash = sha1($adminPassword.$salt);
	$tokenAuth = md5( uniqid().time().DOMAIN );

	$data = array(
		'admin'=>array(
			'nickname'=>'Admin',
			'firstName'=>$L->get('Administrator'),
			'lastName'=>'Axcora',
			'role'=>'admin',
			'password'=>$passwordHash,
			'salt'=>$salt,
			'email'=>'axcora@gmail.com',
			'registered'=>$currentDate,
			'tokenRemember'=>'',
			'tokenAuth'=>$tokenAuth,
			'tokenAuthTTL'=>'2009-03-15 14:00',
			'twitter'=>'https://www.axcora.com',
			'facebook'=>'https://axcora.com',
			'instagram'=>'',
			'codepen'=>'',
			'linkedin'=>'',
			'xing'=>'',
			'github'=>'',
			'gitlab'=>''
		)
	);
	file_put_contents(PATH_DATABASES.'users.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File syslog.php
	$data = array(
		array(
			'date'=>$currentDate,
			'dictionaryKey'=>'👋 Welcome to rent car cms',
			'notes'=>'',
			'idExecution'=>uniqid(),
			'method'=>'POST',
			'username'=>'admin'
	));
	file_put_contents(PATH_DATABASES.'syslog.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File security.php
	$data = array(
		'minutesBlocked'=>5,
		'numberFailuresAllowed'=>10,
		'blackList'=>array()
	);
	file_put_contents(PATH_DATABASES.'security.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File categories.php
	$data = array(
		'blog'=>array('name'=>'Blog', 'description'=>'', 'template'=>'', 'list'=>$slugs),
		'news'=>array('name'=>'News', 'description'=>'', 'template'=>'', 'list'=>array()),
		'sport'=>array('name'=>'Sport', 'description'=>'', 'template'=>'', 'list'=>array())
	);
	file_put_contents(PATH_DATABASES.'categories.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File tags.php
	$data = array();
	file_put_contents(PATH_DATABASES.'tags.php', $dataHead.json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);

	// File plugins/about/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'about'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>1,
				'label'=>$L->get('About'),
				'text'=>$L->get('A new modern website flatfile content management system
				
				built with oto axcora bludit flatfile cms for develope blast fast website with auto SEO injection !!
				
				Documentation :
				1. Login on admin area panel with visit yoursite/admin
				2. Insert username admin and password you have create from first installation
				3. Configure on setting - general  and insert title description and settup your site
				4. Activate plugins - visit on plugins - and need to activate RSS, Sitemap , open graph , twitter card , custom field , and all oto plugins
				5. Write content - Need to create categories first for grouping your article with click on categories and create new , now you can write content article - just click new content and insert title , article , click on Options on article form - general and insert your description - change tab advance insert tags
				
				Read more documentation on :  https://cms.granlimousine.com/download
				
				



')
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File plugins/simple-stats/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'simple-stats'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'numberOfDays'=>7,
				'label'=>$L->get('Visits'),
				'excludeAdmins'=>false,
				'position'=>1
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);
	mkdir(PATH_WORKSPACES.'simple-stats', DIR_PERMISSIONS, true);

	// File plugins/tinymce/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'tinymce'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>1,
				'toolbar1'=>'formatselect bold italic forecolor backcolor removeformat | bullist numlist table | blockquote alignleft aligncenter alignright | link unlink pagebreak image code',
				'toolbar2'=>'',
				'plugins'=>'code autolink image link pagebreak advlist lists textpattern table'
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File plugins/canonical/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'canonical'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>1
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	// File plugins/robots/db.php
	file_put_contents(
		PATH_PLUGINS_DATABASES.'robots'.DS.'db.php',
		$dataHead.json_encode(
			array(
				'position'=>1,
				'robotstxt'=>'User-agent: *'.PHP_EOL.'Allow: /'
			),
		JSON_PRETTY_PRINT),
		LOCK_EX
	);

	return true;
}

function redirect($url) {
	if (!headers_sent()) {
		header("Location:".$url, TRUE, 302);
		exit;
	}

	exit('<meta http-equiv="refresh" content="0; url="'.$url.'">');
}

// ============================================================================
// MAIN
// ============================================================================

if (alreadyInstalled()) {
	$errorText = 'Axcora CMS Axcora Bludit is already installed ;)';
	error_log('[ERROR] '.$errorText, 0);
	exit($errorText);
}

// Install a demo, just call the install.php?demo=true
if (isset($_GET['demo'])) {
	install('demo123', 'UTC');
	redirect(HTML_PATH_ROOT);
}

// Install by POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (Text::length($_POST['password'])<6) {
		$errorText = $L->g('password-must-be-at-least-6-characters-long');
		error_log('[ERROR] '.$errorText, 0);
	} else {
		install($_POST['password'], $_POST['timezone']);
		redirect(HTML_PATH_ROOT);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $L->get('Oto CMS Axcora Bludit Installer') ?></title>
	<meta charset="<?php echo CHARSET ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex,nofollow">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

	<!-- Favicon -->

<link rel="icon" type="image/png" href="https://mesinkasironline.web.app/img/createwebsiteusingangular.png">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="bl-kernel/css/bootstrap.min.css?version=<?php echo time() ?>">
	<link rel="stylesheet" type="text/css" href="bl-kernel/admin/themes/booty/css/bludit.css?version=<?php echo time() ?>">

	<!-- Javascript -->
	<script charset="utf-8" src="bl-kernel/js/jquery.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/bootstrap.bundle.min.js?version=<?php echo time() ?>"></script>
	<script charset="utf-8" src="bl-kernel/js/jstz.min.js?version=<?php echo time() ?>"></script>
</head>
<body class="login">
<div class="container">
	<div class="row justify-content-md-center pt-5">
		<div class="col-md-4 pt-5 text-center">
<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="currentColor" class="bi bi-car-front" viewBox="0 0 16 16">
  <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17 1.247 0 2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276Z"/>
  <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.807.807 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155 1.806 0 4.037-.084 5.592-.155A1.479 1.479 0 0 0 15 9.611v-.413c0-.099-.01-.197-.03-.294l-.335-1.68a.807.807 0 0 0-.43-.563 1.807 1.807 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3H4.82Z"/>
</svg>
			<h1 class="text-center mb-5 mt-5 font-weight-normal text-uppercase" style="color: #555;"><?php echo $L->get('RENT CAR CMS Installer') ?></h1>
			<p class="lead">A flatfile cms for built fast modern website app present by axcora technology bludit</p>
			<p>If you need built new website project with flatfile cms so you can <a href="https://www.fiverr.com/creativitas/design-your-website-with-phyton-django">hire our team →</a></p>
			<?php
			$system = checkSystem();
			if (!empty($system)) {
				foreach ($system as $error) {
					echo '
					<table class="table">
						<tbody>
							<tr>
								<th>'.$error.'</th>
							</tr>
						</tbody>
					</table>
					';
				}
			}
			elseif (isset($_GET['language']))
			{
			?>
				<!-- <p><?php echo $L->get('choose-a-password-for-the-user-admin') ?></p> -->
				<p>Default user is admin , but you can change later, please input your password backend.</p>
				<?php if (!empty($errorText)): ?>
				<div class="alert alert-danger"><?php echo $errorText ?></div>
				<?php endif ?>

				<form id="jsformInstaller" method="post" action="" autocomplete="off">
					<input type="hidden" name="timezone" id="jstimezone" value="UTC">

					<div class="form-group">
					<input type="text" value="admin" class="form-control form-control-lg" id="jsusername" name="username" placeholder="Username" disabled>
					</div>

					<div class="form-group mb-0">
					<input type="password" class="form-control form-control-lg" id="jspassword" name="password" placeholder="<?php $L->p('Password') ?>">
					</div>
					<div id="jsshowPassword" style="cursor: pointer;" class="text-center pt-0 text-muted"><?php $L->p('Show password') ?></div>

					<div class="form-group mt-4">
					<button type="submit" class="btn btn-dark btn-lg mr-2 w-100" name="install"><?php $L->p('Install') ?></button>
					</div>
				</form>
			<?php
			}
			else
			{
			?>
				<form id="jsformLanguage" method="get" action="" autocomplete="off">
					<label for="jslanguage"><?php echo $L->get('Choose your language') ?></label>
					<select id="jslanguage" name="language" class="form-control form-control-lg">
					<?php
						$htmlOptions = getLanguageList();
						foreach($htmlOptions as $fname=>$native) {
							echo '<option value="'.$fname.'"'.( ($finalLanguage===$fname)?' selected="selected"':'').'>'.$native.'</option>';
						}
					?>
					</select>

					<div class="form-group mt-4">
					<button type="submit" class="btn btn-primary btn-lg mr-2 w-100"><?php $L->p('Next') ?></button>
					</div>
				</form>
			<?php
			}
			?>
		</div>
	</div>
</div>

<script>
$(document).ready(function()
{
	// Timezone
	var timezone = jstz.determine();
	$("#jstimezone").val( timezone.name() );

	// Show password
	$("#jsshowPassword").on("click", function() {
		var input = document.getElementById("jspassword");

		if(input.getAttribute("type")=="text") {
			input.setAttribute("type", "password");
		}
		else {
			input.setAttribute("type", "text");
		}
	});

});
</script>

</body>
</html>
