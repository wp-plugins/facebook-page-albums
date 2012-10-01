<?php
/*
 * This file is part of facebook-page-albums.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DB Manager
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbumsDBManager {
	protected $api_config = 'facebook_page_albums_api_config';

	/**
	 * constructer
	 */
	public function __construct() {
	}

	/**
	 * Excute when this plugin is actived
	 */
	public function initialize() {
	}

	/**
	 * Excute when this plugin is deactived
	 */
	public function destroy() {
		delete_option($this->api_config);
	}

	/**
	 * get api option
	 */
	public function get_api_option() {
		return get_option($this->api_config);
	}

	/**
	 * set api config
	 */
	public function set_api_option($args=array()) {
		$defaults = array(
			'appId'     => '',
			'secret'  => '',
			'pageId'  => ''
		);
		$args = wp_parse_args( $args, $defaults );
		return update_option($this->api_config, $args);
	}

}?>