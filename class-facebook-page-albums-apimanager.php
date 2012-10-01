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
		require_once( 'lib/facebook.php' );
		require_once( 'class-facebook-page-albums-dbmanager.php' );
		$this->init();
	}

	/**
	 * Create api instance.
	 */
	private function init() {
		if (empty($this->db)) {
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
		$this->client = new Facebook($config);
		return true;
	}

	/**
	 * Get data by using facebook graph api.
	 *
	 * @param  string  $query  Graph API Query
	 * @param  string  $param  Parameter
	 * @return array
	 */
	public function get($query=null, $param=null) {
		if (empty($query)) {
			$query = $this->page_id;
		}
		if (empty($query) || empty($this->client)) {
			return false;
		}
		$slug = '/' . $query;
		if (!empty($param)) {
			$slug .= '?' . $param;
		}
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
	 * @param  string  $page_id     Facebook Page ID/Slug
	 * @param  boolean $cover_photo if true, get the cover_photo information.
	 * @return array
	 */
	public function get_albums($page_id=null, $cover_photo=true) {
		$albums = $this->get($page_id, 'fields=albums');
		if (empty($albums['albums']['data']) || !$cover_photo) {
			return $albums['albums']['data'];
		}

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
	 * @param integer $album_id album id
	 * @param integer $args     limit and offset
	 * @return array
	 */
	public function get_photos($album_id, $args=null) {
		$defaults = array(
			'per_page' => 25,
			'paged'    => 1
		);
		$args = wp_parse_args( $args, $defaults );
		$param = array();
		if (!empty($args['per_page'])) {
			$param[] = 'limit=' . $args['per_page'];
		}
		if (!empty($args['paged'])) {
			$param[] = 'offset=' . $args['paged'];
		}
		$photos = $this->get($album_id . '/photos', implode('&', $param));

		if (!empty($photos['data'])) {
			return $photos['data'];
		} else {
			return $photos;
		}
	}

}
?>