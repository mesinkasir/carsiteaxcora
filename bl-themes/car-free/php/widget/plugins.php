<section class="bg-primary">
<div class="wrap size-50">
<ul class="text-cols">
<?php Theme::plugins('siteSidebar') ?> 
<?php foreach (Theme::socialNetworks() as $key=>$label): ?><dt>
<a href="<?php echo $site->{$key}(); ?>" target="_blank"><?php echo $label; ?>.</a></dt><?php endforeach; ?>
<?php if (Theme::rssUrl()): ?><dt class="mt-4 font-semibold text-white"><a href="<?php echo Theme::rssUrl() ?>" target="_blank">RSS</a></dt><?php endif; ?>
</ul>
</div>
</section>