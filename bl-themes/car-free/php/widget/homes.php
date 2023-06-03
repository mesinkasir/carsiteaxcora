<section class="fullscreen bg-black">
<div class="wrap aligncenter fadeInUp size-50">
<?php if ($site->logo()): ?><img src="<?php echo $site->logo(); ?>" alt="<?php echo $site->title(); ?>" width="240" height="480"/><?php endif ?>
<h1><strong><a href="<?php echo Theme::siteUrl(); ?>"><?php echo $site->title(); ?></a></strong></h1>
<?php if ($site->description()): ?><h2><?php echo $site->description(); ?></h2><?php endif ?>
<?php if ($site->slogan()): ?><p><?php echo $site->slogan(); ?></p><?php endif ?>
</div>
</section>