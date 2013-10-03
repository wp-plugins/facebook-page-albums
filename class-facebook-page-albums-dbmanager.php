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
	protected $album_data_config = 'facebook_page_albums_album_data';
	protected $album_data_update_config = 'facebook_page_albums_album_data_update';

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
		delete_option( $this->api_config );
		delete_option( $this->common_config );
		delete_option( $this->album_data_config );
		delete_option( $this->album_data_update_config );
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
	 */
	public function set_common_option( $args=array() ) {
		$defaults = array(
			'enable_album_cache' => false
		);
		$args = wp_parse_args( $args, $defaults );
		return update_option( $this->common_config, $args );
	}


	/**
	 *
	 */
	public function get_album_list() {
		// @todo pagination
		return get_option( $this->album_data_config );
	}


	/**
	 *
	 */
	public function save_album_list( $data ) {
		// @todo use table of database.
		update_option( $this->album_data_config, $data );
		if (!empty($data)) {
			update_option( $this->album_data_update_config, current_time('timestamp') );
		} else {
			delete_option( $this->album_data_update_config );
		}
		return true;
	}


	/**
	 *
	 */
	public function get_album_list_updated_time() {
		// @todo pagination
		return get_option( $this->album_data_update_config );
	}
}?>