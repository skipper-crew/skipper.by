<?php
/*
Plugin Name: Advanced Edit Cforms
Plugin URI: http://www.beapi.fr/
Description: Allow to edit some parameters of Cforms as hardcoded variable and base url ! Very useful when you move or rename a WordPress blog.
Author: Amaury Balmer
Author URI: http://www.beapi.fr/
Version: 1.0.4
*/

add_filter( 'plugins_loaded', 'initAdvancedCforms' );
function initAdvancedCforms() {
	global $adv_cforms;
	
	// Localization
	load_plugin_textdomain ( 'adv-cforms', false, 'advanced-edit-cforms/languages/' );
	
	if ( is_admin() )
		$adv_cforms = new Advanced_Cforms();
}

class Advanced_Cforms {
	var $option_name 		= 'cforms_settings';
	var $editables_options 	= array( 'cforms_root', 'tinyURI', 'cforms_root_dir', '_upload_dir' );
	var $abspath_file 		= '';
	var $cforms_js_file 	= '';
	
	/**
	 * Constructor, init menu
	 *
	 * @return void
	 * @author Amaury Balmer
	 */
	function Advanced_Cforms() {
		if ( is_dir(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'cforms') ) {
			$this->abspath_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'cforms' . DIRECTORY_SEPARATOR . 'abspath.php';
			$this->cforms_js_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'cforms' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'cforms.js';
			
			add_action( 'admin_menu', array(&$this, 'addMenu') );
		}
	}
	
	/**
	 * Add page cforms on menu
	 *
	 * @return void
	 * @author Amaury Balmer
	 */
	function addMenu() {
		add_options_page( __('Adv Edit Cforms', 'adv-cforms'), __('Adv Edit Cforms', 'adv-cforms'), 'manage_options', 'adv-cforms', array( &$this, 'pageOptions' ) );
	}
	
	/**
	 * Display many options of cforms
	 *
	 * @return void
	 * @author Amaury Balmer
	 */
	function pageOptions() {
		if ( isset($_POST['submit_adv_cforms']) ) {
			if ( !current_user_can('manage_options') )
				die(__( 'You cannot edit the Advanced Cforms options.', 'adv-cforms' ));
			
			check_admin_referer( 'adv-cforms-options' );
			
			// DB Options
			if ( isset($_POST['cforms']) ) {
				$current_options = get_option( $this->option_name );
				$current_options = $this->array_merge_recursive_distinct( $current_options, $_POST['cforms'] );
				update_option( $this->option_name, $current_options );
			}
			
			// File abs path
			if ( isset($_POST['abs-path-file']) && is_writable($this->abspath_file) ) {
				$_POST['abs-path-file'] = stripslashes($_POST['abs-path-file']);
				if ( file_get_contents( $this->abspath_file ) != $_POST['abs-path-file'] ) {
					file_put_contents( $this->abspath_file, $_POST['abs-path-file'] );
				}
			}
			
			// JS File
			if ( isset($_POST['cforms-js-file']) && is_writable($this->cforms_js_file) ) {
				$new_file = array();
				$_POST['cforms-js-file'] = stripslashes($_POST['cforms-js-file']);
				$lines = file( $this->cforms_js_file );
				foreach ($lines as $line_num => $line) {
					if ( strpos( $line, 'var sajax_uri =' ) !== false ) {
						$new_file[] = $_POST['cforms-js-file'];
					} else {
						$new_file[] = $line;
					}
				}
				file_put_contents( $this->cforms_js_file, implode( '', $new_file ) );
			}
			
			echo '<div id="message" class="updated"><p>'.__('Options updated with succes !', 'adv-cforms').'</p></div>';
		}
		
		clearstatcache();
		$current_options = get_option( $this->option_name );
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e("Advanced Edit Cforms Options", 'adv-cforms'); ?></h2>
			
			<form action="" method="post">
				<?php wp_nonce_field( 'adv-cforms-options' ); ?>
				
				<?php if ( $current_options != false ) : ?>
					<table class="form-table">
						<?php
						foreach( (array) $current_options as $current_option => $options ) :
							foreach( (array) $options as $option_key => $option_value ) :
							if ( !in_array($option_key, $this->editables_options) ) {
								if ( strpos( $option_key, '_upload_dir' ) === false ) { // cforms*_upload_dir
									continue;
								}
							}
							?>
							<tr>
								<th>
									<label for="<?php echo esc_attr($option_key); ?>"><?php echo esc_html($option_key); ?></label>
								</th>
								<td>
									<input style="width:100%" type="text" id="<?php echo esc_attr($option_key); ?>" name="cforms[<?php echo esc_attr($current_option); ?>][<?php echo esc_attr($option_key); ?>]" value="<?php echo esc_attr($option_value); ?>" />
								</td>
							</tr>
							<?php
							endforeach;
						endforeach;
						?>
					</table>
					<br/>
				<?php else : ?>
					<p><?php _e('No options available on your database. Have you already install Cforms on your WordPress ?', 'adv-cforms'); ?></p>
				<?php endif; ?>
				
				<?php if( is_file($this->abspath_file) ) : ?>
					<table class="form-table">
						<tr>
							<th>
								<label for="abs-path-file"><?php _e('Abs Path File', 'adv-cforms'); ?></label>
							</th>
							<td>
								<?php
								if ( !is_writable($this->abspath_file) ) {
									echo __('File not writeable.', 'adv-cforms'). '<br />';
									chmod( $this->abspath_file, 0644 );
								}
								?>
								<textarea style="width:100%" id="abs-path-file" name="abs-path-file"><?php echo file_get_contents( $this->abspath_file ); ?></textarea>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				
				<?php if( is_file($this->cforms_js_file) ) : ?>
					<table class="form-table">
						<tr>
							<th>
								<label for="cforms-js-file"><?php _e('JS File (sAJAX uri)', 'adv-cforms'); ?></label>
							</th>
							<td>
								<?php
								if ( !is_writable($this->cforms_js_file) ) {
									echo __('File not writeable.', 'adv-cforms'). '<br />';
									chmod( $this->cforms_js_file, 0644 );
								}
							
								$lines = file( $this->cforms_js_file );
								$cforms_js_line = '';
								foreach ($lines as $line_num => $line) {
									if ( strpos( $line, 'var sajax_uri =' ) !== false ) {
										$cforms_js_line = $line;
										break;
									}
								}
								?>
								<textarea style="width:100%" id="cforms-js-file" name="cforms-js-file"><?php echo $cforms_js_line; ?></textarea>
							</td>
						</tr>
					</table>
				<?php endif; ?>
				
				<p class="submit">
					<input type="submit" class="button" name="submit_adv_cforms" value="<?php _e("Save Settings", 'adv-cforms'); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Helper for merge recursive array
	 *
	 * @author Amaury Balmer
	 */
	function array_merge_recursive_distinct ( &$array1, &$array2 ) {
		$merged = $array1;
		
		foreach ( $array2 as $key => &$value ) {
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
				$merged [$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
			} else {
				$merged [$key] = $value;
			}
		}
		
		return $merged;
	}
}
?>