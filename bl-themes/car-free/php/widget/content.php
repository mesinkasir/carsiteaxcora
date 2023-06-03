<?php Theme::plugins('pageBegin'); ?>
<?php foreach ($content as $page): ?>
<section>
<div class="wrap size-70">
<div class="grid">
<div class="column">
<figure>
<div class="embed">
<?php if ($page->coverImage()): ?><a href="<?php echo $page->permalink(); ?>"><img src="<?php echo $page->coverImage(); ?>" loading="lazy" width="460" height="320" style="max-width:400px; max-height:500px;" alt="<?php echo $page->title(); ?>"></a><?php endif ?>
</div>
</figure>
</div>
<div class="column">
<h2><strong><a href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a></strong></h2>
<hr/>
<?php if ($page->description()): ?><p class="text-intro"><?php echo $page->description(); ?></p><?php endif ?>
<p>
<a href="<?php echo $page->permalink(); ?>" class="button"><?php echo $page->title(); ?> Info</a>
</p>
</div>
</div>
</div>
</section>
<?php endforeach ?>