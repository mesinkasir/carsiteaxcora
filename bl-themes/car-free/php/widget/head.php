<!DOCTYPE html>
<html lang="<?php echo Theme::lang() ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php echo Theme::metaTagTitle(); ?>
<?php echo Theme::metaTagDescription(); ?>
<?php echo Theme::favicon('img/logo.webp'); ?>
<?php Theme::plugins('siteHead'); ?>
<?php echo Theme::css('css/style.css'); ?>
<?php echo Theme::css('css/svg-icon.css'); ?>
</head>