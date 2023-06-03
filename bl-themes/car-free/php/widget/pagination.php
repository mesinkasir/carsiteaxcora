<?php if (Paginator::numberOfPages()>1): ?>
<section class="bg-secondary">
<div class="wrap size-50">
<h3>Page List</h3>
<p>
Explore all page
</p>
<nav>
<?php if (Paginator::showPrev()): ?>
<a href="<?php echo Paginator::previousPageUrl() ?>" class="button">
<span class="sr-only">Previous</span>
</a>
<?php endif ?>
<a href="<?php echo Theme::siteUrl() ?>" aria-current="page" class="button ghost">Home</a>
<?php if (Paginator::showNext()): ?>
<a href="<?php echo Paginator::nextPageUrl() ?>" class="button">
<span class="sr-only">Next</span>
</a>
<?php endif ?>
</nav>
</div>
</section>
<?php endif ?>
<?php Theme::plugins('pageEnd'); ?>