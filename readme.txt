=== Plugin Name ===
Contributors: daiki.suganuma
Tags: facebook, facebook pages, facebook graph api, album, photos
Requires at least: 3.2
Tested up to: 3.4
Stable tag: 1.0.0
License: Apache License Version 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

Get the all albums from Facebook Page.

== Description ==

`Facebook Page Albums` connect <a href="https://developers.facebook.com/docs/reference/api/">Facebook Graph API</a>.
Once set up the API key and Facebook Page's address, You can get the page's albums and photos.

This version provide a few functions, you have to develop gallery page by using html and javascript.

<h4>Demo</h4>

<a href="http://www.jka.sg/gallery/">Gallery | JKA Singapore</a> is created by using this plugin.

== Installation ==

1. Upload `facebook-page-albums` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up in 'Facebook Page Albums' menu
4. Call `facebook_page_albums_get_album_list` function in your theme. you can get album list and develop anything you like.

== Screenshots ==

1. **Setting** - you can set up the API key and Facebook page address.
2. **Album List** - `facebook_page_albums_get_album_list` provide the album list.
3. **Photo List** - `facebook_page_albums_get_photo_list` provide the photo list.

== Changelog ==

= 1.0 =
* First release.
