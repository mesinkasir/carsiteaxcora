<header role="banner bg-dark">
      <nav role="navigation">
        <p><a href="<?php echo Theme::siteUrl(); ?>" title="<?php echo $site->title(); ?>">
		<img src="<?php echo $site->logo(); ?>" alt="<?php echo $site->title(); ?>" width="120" height="60"/>
		<?php echo $site->title(); ?></a></p>
        <ul>
          <li>
		  <a href="<?php echo Theme::siteUrl(); ?>">Home</a>
          </li>
          <?php foreach ($staticContent as $staticPage): ?>
          <li>
		<a href="<?php echo $staticPage->permalink(); ?>"><?php echo $staticPage->title(); ?></a>
		</li>
        <?php endforeach ?>
		</ul>
      </nav>
</header>