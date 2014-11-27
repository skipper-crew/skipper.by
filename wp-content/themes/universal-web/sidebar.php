<div class="sidebar">

			<?php
			if (!dynamic_sidebar('sidebar-widget-area') ) : ?>
			<div class="widget">
				<h3><?php _e( 'Pages', 'universal_web' ); ?></h3>
				<ul>
			        <?php wp_list_pages('title_li='); ?>
				</ul>
			</div>
			<div class="widget">
				<h3><?php _e( 'Categories', 'universal_web' ); ?></h3>
				<ul>
			        <?php wp_list_categories('title_li='); ?>
				</ul>
			</div>
			<div class="widget">
				<h3><?php _e( 'Archives', 'universal_web' ); ?></h3>
				<ul>
					<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</div>
			<?php /* If this is the frontpage */ if ( is_home() || is_page() ) { ?>
			<div class="widget">
				<h3><?php _e( 'Meta', 'universal_web' ); ?></h3>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</div>

			<?php } ?>
			<?php endif; ?>

</div>