<?php
/*
 Plugin Name: Facebook Page Albums
 Plugin URI: http://wordpress.org/extend/plugins/facebook-page-albums/
 Description: Get the all albums/photos from your Facebook Page.
 Version: 1.1.1
 Author: Daiki Suganuma
 Author URI: http://se-suganuma.blogspot.com/
 */

/**
 *  Copyright 2014 Daiki Suganuma  (email : daiki.suganuma@gmail.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/***** Define Part *****/
define('FACEBOOK_PAGE_ALBUMS_CACHE_GROUP', 'facebook_page_albums');
define('FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT', 60 * 60 ); //60 minutes

if ( is_admin() ) {
	require_once( 'facebook-page-albums-admin.php' );
}


class FacebookPageAlbums {
	private $api = null;
	private $db = null;
	private $config = null;


	/**
	 * constructer
	 */
	public function __construct() {
		require_once( 'class-facebook-page-albums-dbmanager.php' );
		$this->db = new FacebookPageAlbumsDBManager();
		$this->config = $this->db->get_common_option();
	}


	public function load_api() {
		if ( empty($this->api) ) {
			require_once('class-facebook-page-albums-apimanager.php');
			$this->api = new FacebookPageAlbumsAPIManager();
		}
	}


	public function get_album_list( $args=array() ) {
		$result = null;

		// Get from cache data.
		if ( $this->config['enable_album_cache'] ) {
			$result = $this->db->get_album_list( $args );
		}

		if ( empty($result) ) {
			$this->load_api();
			$result = $this->api->get_albums( $args );

			// Save cache data.
			if ( !empty($result) && $this->config['enable_album_cache'] ) {
				$this->db->save_album_list( $result );
			}
		}

		return $result;
	}


	public function get_album_info( $album_id ) {
		if ( empty($album_id) ) {
			return false;
		}

		$this->load_api();
		return $this->api->get( $album_id );
	}


	public function get_photo_list( $args=array() ) {
		$this->load_api();
		return $this->api->get_photos( $args );
	}
}


/**
 * Get album list.
 *
 * @return array album list
 */
function facebook_page_albums_get_album_list( $args=array() ) {
	// Get Object Cache
	$cache_name = 'album_list' . implode('', $args);
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from API
		global $facebook_page_albums;

		if ( empty($facebook_page_albums) ) {
			$facebook_page_albums = new FacebookPageAlbums();
		}
		$result = $facebook_page_albums->get_album_list( $args );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	return $result;
}


/**
 * Get album information.
 *
 * @param  integer $album_id
 * @return array album information
 */
function facebook_page_albums_get_album( $album_id ) {
	// Get Object Cache
	$cache_name = 'album_info' . $album_id;
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from API
		global $facebook_page_albums;

		if ( empty($facebook_page_albums) ) {
			$facebook_page_albums = new FacebookPageAlbums();
		}
		$result = $facebook_page_albums->get_album_info( $album_id );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	return $result;
}


/**
 * Get photo list.
 *
 * @param  integer $album_id album id
 * @param  array $args arguments
 * @return array             photo list
 */
function facebook_page_albums_get_photo_list( $album_id, $args=array() ) {
	// Get Object Cache
	$cache_name = 'photo_list' . $album_id . implode('', $args);
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from API
		global $facebook_page_albums;

		if ( empty($facebook_page_albums) ) {
			$facebook_page_albums = new FacebookPageAlbums();
		}
		$args['album_id'] = $album_id;
		$result = $facebook_page_albums->get_photo_list( $args );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	return $result;
}


/**
 * This function will fire if 'Enable Cache' is enable on admin panel.
 */
function facebook_page_albums_get_album_list_cron() {
	// Get all albums from facebook
	require_once('class-facebook-page-albums-apimanager.php');
	require_once( 'class-facebook-page-albums-dbmanager.php' );

	$api = new FacebookPageAlbumsAPIManager();
	$db = new FacebookPageAlbumsDBManager();
	if ( $result = $api->get_albums( array('per_page' => 0) ) ) {
		$db->save_album_list( $result );
	}
	return true;
}
add_action('facebook_page_albums_cron_hook', 'facebook_page_albums_get_album_list_cron');


/**
 * Add cron schedule time for debug
 */
function cron_add_debug( $schedules ) {
	$schedules['debug'] = array(
		'interval' => 60,
		'display' => __( '1min for Debug' )
		);
	return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_debug' );
?>