<?php Theme::plugins('pageBegin'); ?>  
<section class="fullscreen bg-black">
<?php if ($page->coverImage()): ?>
<span class="background" 
style="background-image:url('<?php echo $page->coverImage(); ?>')
"></span><?php endif ?>
<div class="wrap aligncenter fadeInUp size-50" style="opacity: 0.9;">
<h1><strong><a href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a></strong></h1>
</div>
</section>