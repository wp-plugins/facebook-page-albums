<?php
/*
 * This file is part of facebook-page-albums.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * API Manager
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbumsAPIManager {
	public   $client  = null;
	public   $error   = array();
	private  $db      = null;
	private  $page_id = null;


	/**
	 * constructer
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Create api instance.
	 */
	private function init() {
		require_once( 'lib/facebook.php' );
		require_once( 'class-facebook-page-albums-dbmanager.php' );

		//
		// Get Config
		//
		if (empty($this->db)) {
			//@see class-facebook-page-albums-dbmanager.php
			$this->db = new FacebookPageAlbumsDBManager();
		}
		$config = $this->db->get_api_option();
		if (empty($config['appId']) || empty($config['secret'])) {
			return false;
		}
		if (!isset($config['fileUpload'])) {
			$config['fileUpload'] = false; // optional
		}
		$this->page_id = $config['pageId'];

		//@see lib/facebook.php
		$this->client = new Facebook($config);

		return true;
	}


	/**
	 * Get data by using facebook graph api.
	 *
	 * @param  string $query  Graph API Query
	 * @param  string || array $param  Parameter
	 * @return array
	 */
	public function get($query=null, $params=array()) {
		if (empty($query)) {
			$query = $this->page_id;
		}
		if (empty($query) || empty($this->client)) {
			return false;
		}

		//
		// Build query string
		//
		$slug = '/' . $query;
		if (!empty($params)) {
			if (is_array($params)) {
				$params = implode('&', $params);
			}
			$slug .= '?' . $params;
		}


		//
		// Send query through Facebook PHP SDK
		//
		try {
			$result = $this->client->api($slug);
		} catch (FacebookApiException $e) {
			error_log($e);
			$result = false;
		}
		return $result;
	}


	/**
	 * Get Album list of Facebook Page
	 *
	 * @param  array  $args    Arguments.
	 * @see  https://developers.facebook.com/docs/reference/api/album/
	 * @return array
	 */
	public function get_albums($args=array()) {
		$defaults = array(
			'page_id' => $this->page_id,
			'cover_photo' => true,
			'per_page' => 25,
			'paged'    => 1
		);
		$args = wp_parse_args($args, $defaults);


		//
		// Build pagenation parameters
		//
		$params = array();
		$params[] = 'fields=albums';
		if (!empty($args['per_page'])) {
			$params[] = 'limit=' . $args['per_page'];
			if (!empty($args['paged'])) {
				$params[] = 'offset=' . ($args['paged'] - 1) * $args['per_page'];
			}
		}

		// Get
		$albums = $this->get($args['page_id'], $params);

		// Do not need the Cover Photo
		if (empty($albums['albums']['data']) || empty($args['cover_photo'])) {
			return $albums['albums']['data'];
		}


		//
		// Get Cover Photo Data through Facebook API
		//
		$data = array();
		foreach ($albums['albums']['data'] as $item) {
			if (empty($item['cover_photo'])) continue;
			$item['cover_photo_data'] = $this->get($item['cover_photo']);
			$data[] = $item;
		}
		return $data;
	}


	/**
	 * Get photos
	 *
	 * @param array   $args     limit and offset
	 * @return array
	 */
	public function get_photos($args=null) {
		$defaults = array(
			'album_id' => null,
			'per_page' => 25,
			'paged'    => 1
		);
		$args = wp_parse_args($args, $defaults);

		if (empty($args['album_id'])) return false;


		//
		// Build pagenation parameters
		//
		$params = array();
		if (!empty($args['per_page'])) {
			$params[] = 'limit=' . $args['per_page'];
			if (!empty($args['paged'])) {
				$params[] = 'offset=' . ($args['paged'] - 1) * $args['per_page'];
			}
		}

		// Send
		$photos = $this->get($args['album_id'] . '/photos', $params);

		if (!empty($photos['data'])) {
			return $photos['data'];
		} else {
			return $photos;
		}
	}

}
?>