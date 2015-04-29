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
	protected $common_config = 'facebook_page_albums_common_config';
	protected $album_data = 'facebook_page_albums_album_data';
	protected $album_data_update = 'facebook_page_albums_album_data_update';


	/**
	 * Constructor
	 */
	public function __construct() {
	}


	/**
	 * Execute when this plugin is activated
	 */
	public function initialize() {
	}


	/**
	 * Execute when this plugin is deactivated
	 */
	public function destroy() {
		delete_option( $this->api_config );
		delete_option( $this->common_config );
		delete_option( $this->album_data );
		delete_option( $this->album_data_update );
	}


	/**
	 * get api option
	 */
	public function get_api_option() {
		$options = get_option( $this->api_config );

		return wp_parse_args( $options, array(
			'appId'     => '',
			'secret'  => '',
			'pageId'  => ''
			) );
	}


	/**
	 * set api option
	 *
	 * @param Array  $args {
	 *   @type String appId
	 *   @type String secret
	 *   @type String pageId
	 * }
	 * @return Array
	 */
	public function set_api_option( $args=array() ) {
		$defaults = array(
			'appId'     => '',
			'secret'  => '',
			'pageId'  => ''
		);
		$args = wp_parse_args( $args, $defaults );
		return update_option( $this->api_config, $args );
	}


	/**
	 * get common option
	 */
	public function get_common_option() {
		$options = get_option( $this->common_config );

		return wp_parse_args( $options, array(
			'enable_album_cache' => false
			) );
	}


	/**
	 * set common option
	 *
	 * @param Array  $args {
	 *   @type Boolean enable_album_cache
	 * }
	 * @return Array
	 */
	public function set_common_option( $args=array() ) {
		$defaults = array(
			'enable_album_cache' => false
		);
		$args = wp_parse_args( $args, $defaults );
		return update_option( $this->common_config, $args );
	}


	/**
	 * Get Album List from Database
	 */
	public function get_album_list() {
		// @todo pagination
		return get_option( $this->album_data );
	}


	/**
	 * Save Album List to Database for Cache
	 *
	 * @param Array  $data
	 * @return Boolean
	 */
	public function save_album_list( $data ) {
		// @todo use table of database.
		update_option( $this->album_data, $data );
		if (!empty($data)) {
			update_option( $this->album_data_update, current_time('timestamp') );
		} else {
			delete_option( $this->album_data_update );
		}
		return true;
	}


	/**
	 * Cache Time
	 */
	public function get_album_list_updated_time() {
		// @todo pagination
		return get_option( $this->album_data_update );
	}
}