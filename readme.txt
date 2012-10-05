=== Plugin Name ===
Contributors: daiki.suganuma
Tags: facebook, facebook pages, facebook graph api, album, photos
Requires at least: 3.2
Tested up to: 3.4
Stable tag: 1.0.1
License: Apache License Version 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

Get the all albums from Facebook Page by using Facebook Graph API.

== Description ==

"Facebook Page Albums" connect <a href="https://developers.facebook.com/docs/reference/api/">Facebook Graph API</a>,
then get albums/photos from your Facebook Page.

Once set up the API key and Facebook Page's address in admin panel, 
you can call **`facebook_page_albums_get_album_list`** or **`facebook_page_albums_get_photo_list`** function to get album/photo list.

This version provide only a few functions, you have to develop your gallery page by using html and javascript.

Getting through API is slow, so please use cache system something like <a href="http://wordpress.org/extend/plugins/wp-super-cache/">wp-super-cache</a>.

<h4>Demo</h4>

"<a href="http://www.jka.sg/gallery/">Gallery | JKA Singapore</a>" is created by using this plugin.

== Installation ==

1. Upload `facebook-page-albums` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set up in 'Facebook Page Albums' menu. you can get the API key from <a href="https://developers.facebook.com/apps">Facebook Developers</a>
4. Call `facebook_page_albums_get_album_list` function in your theme. you can get album list and develop anything you like.

== Screenshots ==

1. **Setting** - you can set up the API key and Facebook page address.
2. **Album List** - `facebook_page_albums_get_album_list` function provide the album list.
3. **Photo List** - `facebook_page_albums_get_photo_list` function provide the photo list.

== Frequently Asked Questions ==

= I did activate and set up the Configuration. What's next? =

You have to do PHP coding in your theme. `facebook_page_albums_get_album_list` provide array list.

= I don't know PHP, HTML, JavaScript =

If a lots of request came to me, I will develop the feature providing html and javascript gallery for theme.

== Changelog ==

= 1.0.0 =
* First release.

= 1.0.1 =
* Fixed paging bug.
