	</div></div>

	<div class="footer">
		<?php wp_nav_menu( array('fallback_cb' => 'universal_web_page_menu_flat', 'container' => false, 'depth' => '1', 'theme_location' => 'secondary', 'link_before' => '', 'link_after' => '') ); ?>
		<p><?php _e('Design:', 'universal_web'); ?> <a href="http://webmotive.pl/strony-internetowe">WebMotive</a></p>
	</div>
<?php wp_footer(); ?>	
</body>
</html>