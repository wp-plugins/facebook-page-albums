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

add_action('admin_init', 'facebook_page_albums_admin_init');
add_action('admin_menu', 'facebook_page_albums_add_pages');

/**
 * Initialize for admin
 */
function facebook_page_albums_admin_init() {
	require_once('class-facebook-page-albums-dbmanager.php');
	$db_manager = new FacebookPageAlbumsDBManager();
	register_activation_hook(__FILE__, array($db_manager, 'initialize'));
	register_deactivation_hook(__FILE__, array($db_manager, 'destroy'));
}

/**
 * Add admin menu
 */
function facebook_page_albums_add_pages() {
	add_options_page(__('Facebook Page Albums', 'facebook_page_albums')
					 , __('Facebook Page Albums', 'facebook_page_albums')
					 , 'manage_options'
					 , basename(__FILE__)
					 , 'facebook_page_albums_admin_page');
}

/**
 * First call when admin page load
 */
function facebook_page_albums_admin_page() {
	$db_manager = new FacebookPageAlbumsDBManager();
	$messages = array();

	// API Settings
	$config = $db_manager->get_api_option();
	$defaults = array(
		'appId'     => '',
		'secret'  => '',
		'pageId'  => ''
	);
	$config = wp_parse_args( $config, $defaults );
	if (!empty($_POST) && $_POST['action'] == 'save_api_setting') {
		$config = array();
		$config['appId'] = $_POST['appId'];
		$config['secret'] = $_POST['secret'];
		$config['pageId'] = $_POST['pageId'];
		$db_manager->set_api_option($config);
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

		<!-- Start API Setting -->
		<form method="POST" action="#">
			<h3><?php _e('API Setting', 'facebook_page_albums');?></h3>
			<input type="hidden" name="action" value="save_api_setting" />

			<table class="widefat">
				<tr>
					<th><?php _e('App ID', 'facebook_page_albums'); ?></th>
					<td>
						<input class="regular-text" type="text" name="appId" value="<?php echo $config['appId'];?>"/>
						<span class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('123456789012345', 'facebook_page_albums');?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e('App Secret', 'facebook_page_albums'); ?></th>
					<td>
						<input class="regular-text" type="text" name="secret" value="<?php echo $config['secret'];?>"/>
						<span class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('adcacbdfbe1806b9c15e5c5aa020176a', 'facebook_page_albums');?></span>
					</td>
				</tr>
				<tr>
					<th><?php _e('Page ID/Slug', 'facebook_page_albums'); ?></th>
					<td>
						<input class="regular-text" type="text" name="pageId" value="<?php echo $config['pageId'];?>"/>
						<span class="example"><?php _e('Example: ', 'facebook_page_albums'); _e('cocacola', 'facebook_page_albums');?></span>
					</td>
				</tr>
			</table>
			<div>
				<input class="button-primary" type="submit" value="<?php _e('Save');?>"/>
			</div>
		</form>
		<!--  End  API Setting -->
	</div> <!-- /.wrap -->
	<?php
}?>