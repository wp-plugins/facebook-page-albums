<?php
/*
 Plugin Name: Facebook Page Albums
 Plugin URI:
 Description: Get the All album from Facebook Page.
 Version: 1.0.0
 Author: Daiki Suganuma
 Author URI: 
 */

/**
 *  Copyright 2012 Daiki Suganuma  (email : daiki.suganuma@gmail.com)
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
	require_once('facebook-page-albums-admin.php');
}


/**
 * Get album list.
 *
 * @return array album list
 */
function facebook_page_albums_get_album_list() {
	$cache_name = 'album_list';
	$result = wp_cache_get($cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP);
	if (empty($result)) {
		require_once('class-facebook-page-albums-apimanager.php');
		$api = new FacebookPageAlbumsAPIManager();
		$result = $api->get_albums();
		if (!empty($result)) {
			//set cache
			wp_cache_set($cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT);
		}
	}

	return $result;
}


/**
 * Get album.
 *
 * @return array album list
 */
function facebook_page_albums_get_album($album_id) {
	if (empty($album_id)) {
		return false;
	}
	$cache_name = 'album_' . $album_id;
	$result = wp_cache_get($cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP);
	if (empty($result)) {
		require_once('class-facebook-page-albums-apimanager.php');
		$api = new FacebookPageAlbumsAPIManager();
		$result = $api->get($album_id);
		if (!empty($result)) {
			//set cache
			wp_cache_set($cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT);
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
function facebook_page_albums_get_photo_list($album_id, $args=null) {
	if (empty($album_id)) {
		return false;
	}
	$cache_name = 'photo_list_' . $album_id;
	$result = wp_cache_get($cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP);
	if (empty($result)) {
		require_once('class-facebook-page-albums-apimanager.php');
		$api = new FacebookPageAlbumsAPIManager();
		$result = $api->get_photos($album_id, $args);
		if (!empty($result)) {
			//set cache
			wp_cache_set($cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT);
		}
	}

	return $result;
}
?>