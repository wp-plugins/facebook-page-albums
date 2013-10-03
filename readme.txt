=== Plugin Name ===
Contributors: daiki.suganuma
Tags: facebook, facebook pages, facebook graph api, album, photos
Requires at least: 3.2
Tested up to: 3.6
Stable tag: 1.1.0
License: Apache License Version 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0

Get the all albums from Facebook Page by using Facebook Graph API.

== Description ==

"Facebook Page Albums" get albums/photos from your Facebook Page through <a href="https://developers.facebook.com/docs/reference/api/">Facebook Graph API</a>.

This version provide only a few functions for your Theme, you have to develop the gallery page by yourself using html and javascript.

Once set up the API key and Facebook Page's address in admin panel, 
you can call **`facebook_page_albums_get_album_list`** or **`facebook_page_albums_get_photo_list`** function to get album/photo list.

This plugin is using <a href="https://developers.facebook.com/docs/php/gettingstarted/">Facebook PHP SDK</a>.

### Demo ###

"<a href="http://www.jka.sg/gallery/">Gallery | JKA Singapore</a>" is using this plugin.

== Installation ==

1. Upload `facebook-page-albums` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Facebook Page Albums' and set up. you can get the API key from <a href="https://developers.facebook.com/apps">Facebook Developers</a>
4. Call `facebook_page_albums_get_album_list` php function in your theme, you can get album list as array.

### Example for Album List ###
<pre><code><?php
	$list = facebook_page_albums_get_album_list();
?>
	<ol class="album-list">
		<?php foreach ($list as $item) : ?>
			<?php
			if ($item['type'] != 'normal' || $item['name'] == 'Cover Photos') continue;
			?>
			<li class="album">
				<?php if ($thumb = $item['cover_photo_data']):?>
				<div class="album-thumb">
					<a href="<?php echo add_query_arg('id', $item['id']);?>">
						<img src="<?php echo $thumb['picture'];?>"/>
					</a>
				</div>
				<?php endif; ?>
				<div class="album-info">
					<h5><a href="<?php echo add_query_arg('id', $item['id']);?>"><?php echo $item['name'];?></a></h5>
					<div class="counts">
						<div class="photos-count"><?php echo $item['count'];?></div>
						<?php if (!empty($thumb['comments'])) :?><div class="comments-count"><?php echo count($thumb['comments']['data']);?></div><?php endif;?>
						<?php if (!empty($thumb['likes'])) :?><div class="likes-count"><?php echo count($thumb['likes']['data']);?></div><?php endif;?>
					</div>
				</div>
			</li>
		<?php endforeach;?>
	</ol>
</code></pre>

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

= 1.1.0 =
* Update Facebook PHP SDK v3.2.2
* Add 'Album Cache' option on setting page

