<?php
/*
 * This file is part of facebook-page-albums.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * functions for admin page
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbumsAdmin {
	private $db = null;

	/**
	 * constructer
	 */
	public function __construct() {
		require_once( 'class-facebook-page-albums-dbmanager.php' );
		$this->db = new FacebookPageAlbumsDBManager();

		// Activation & Deactivation
		$main_file = dirname( __FILE__ ) . '/facebook-page-albums.php';
		register_activation_hook($main_file, array($this, 'activation'));
		register_deactivation_hook($main_file, array($this, 'deactivation'));

		// Menu
		add_action('admin_menu', array($this, 'menu'));
	}


	public function activation() {
		$this->db->initialize();
	}


	public function deactivation() {
		$this->db->destroy();

		// Remove cron hook
		wp_clear_scheduled_hook('facebook_page_albums_cron_hook');
	}


	public function menu() {
		add_options_page(__('Facebook Page Albums', 'facebook_page_albums')
						 , __('Facebook Page Albums', 'facebook_page_albums')
						 , 'manage_options'
						 , basename(__FILE__)
						 , array($this, 'admin_page'));
	}


	public function admin_page() {
		$messages = array();

		// Save
		if ( !empty($_POST) && $_POST['action'] == 'save_setting' ) {
			// API Settings
			$config = array();
			$config['appId'] = $_POST['appId'];
			$config['secret'] = $_POST['secret'];
			$config['pageId'] = $_POST['pageId'];
			$this->db->set_api_option($config);

			// Common Settings
			$config = array();
			$config['enable_album_cache'] = (empty($_POST['enable_album_cache'])) ? false : $_POST['enable_album_cache'];
			$this->db->set_common_option($config);

			// Set Cron Job
			wp_clear_scheduled_hook( 'facebook_page_albums_cron_hook' );
			if ($config['enable_album_cache']) {
				// Register cron job
				//wp_schedule_event( time(), 'debug', 'facebook_page_albums_cron_hook' );
				wp_schedule_event( time(), 'hourly', 'facebook_page_albums_cron_hook' );
			} else {
				// Clear Cache
				$this->db->save_album_list(null);
			}

			$messages[] = __('Saved');
		}
?>
	<div class="wrap">
		<h2><?php _e('Facebook Page Albums Configuration', 'facebook_page_albums');?></h2>

		<?php if (!empty($messages)) :?>
		<div class="updated" style="background-color:#FFFBCC;">
			<?php foreach ($messages as $item):?>
			<p><?php echo $item;?></p>
			<?php endforeach;?>
		</div>
		<?php endif;?>

		<form method="POST" action="#">
			<input type="hidden" name="action" value="save_setting" />
			<?php
			// API Settings
			$this->api_settings();
			?>

			<?php
			// Common Settings
			$this->common_settings();
			?>

			<?php submit_button(); ?>
		</form>
	</div> <!-- /.wrap -->
	<?php
	}


	/**
	 * API Settings
	 */
	public function api_settings() {
		$config = $this->db->get_api_option();
	?>
	<h3 class="title"><?php _e('API Settings', 'facebook_page_albums');?></h3>
	<p>
		<?php _e('Get the "App ID" and "App Secret" on <a href="https://developers.facebook.com/apps" target="_blank">Facebook Dev Center</a>.', 'facebook_page_albums');?>
	</p>
	<table class="form-table">
		<tr>
			<th><?php _e('App ID', 'facebook_page_albums'); ?></th>
			<td>
				<input class="regular-text" type="text" name="appId" value="<?php echo $config['appId'];?>"/>
				<p class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('123456789012345', 'facebook_page_albums');?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('App Secret', 'facebook_page_albums'); ?></th>
			<td>
				<input class="regular-text" type="text" name="secret" value="<?php echo $config['secret'];?>"/>
				<p class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('adcacbdfbe1806b9c15e5c5aa020176a', 'facebook_page_albums');?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('Page ID/Slug', 'facebook_page_albums'); ?></th>
			<td>
				<input class="regular-text" type="text" name="pageId" value="<?php echo $config['pageId'];?>"/>
				<p class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('cocacola', 'facebook_page_albums');?></p>
			</td>
		</tr>
	</table>
<?php
	}


	/**
	 * Common Settings
	 */
	public function common_settings() {
		$config = $this->db->get_common_option();
		if ($last_update = $this->db->get_album_list_updated_time()) {
			$last_update = date_i18n('Y/m/d H:i:s', $last_update);
		} else {
			$last_update = '';
		}
	?>
	<h3 class="title"><?php _e('Common Settings', 'facebook_page_albums');?></h3>
	<table class="form-table">
		<tr>
			<th><?php _e('Album Cache', 'facebook_page_albums');?></th>
			<td>
				<input type="checkbox" name="enable_album_cache" value="1" <?php if ( $config['enable_album_cache'] ) echo 'checked';?>/>
				<?php _e('use album cache', 'facebook_page_albums');?>
				<p>
					<?php _e('if enabled, album data will be cache in database. It will be refresh each 1 hour by using WP-CRON.', 'facebook_page_albums');?>
				</p>
				<p>
					<?php _e('Last updated: ', 'facebook_page_albums');?><?php echo $last_update;?>
				</p>
			</td>
		</tr>
	</table>
<?php
	}
}


// Instantiation
new FacebookPageAlbumsAdmin();
?>