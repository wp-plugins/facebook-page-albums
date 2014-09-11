<?php
/**
 * Code Sample of "Facebook Page Albums" plugin
 *
 * @package WordPress
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="main">
		<div id="container">
			<div id="content" role="main">
				<?php
				if (!empty($_GET['id'])) {
					// Photo List
					theme_show_photos($_GET['id']);
				}
				else {
					// Album List
					theme_show_albums();
				}
				?>
			</div><!-- #content -->
		</div><!-- #container -->
	</div>
	<?php wp_footer(); ?>
</body>
</html>
<?php

/**
 * Render the album list
 */
function theme_show_albums() {
//
// Get Album List
//
$list = facebook_page_albums_get_album_list();
if (empty($list)) :
	?>
	<p>
		No album list, but plugin installed correctly.<br/>
		Please check the settings in admin panel.<br/>
		And please check the
		<a href="http://wordpress.org/support/plugin/facebook-page-albums" target="_blank">plugin official site</a>
	</p>
<?php
	return;
endif;
?>
<ol class="album-list">
	<?php
	//
	// Loop Album List and Render items
	//
	if (!empty($list)) : foreach ($list as $item) :
		if ($item['type'] != 'normal' || $item['name'] == 'Cover Photos') {
			continue;
		}

		// Link to each album
		$link = add_query_arg('id', $item['id']);
		?>
		<li class="album">
			<?php
			// It has a thumbnail
			if ($thumb = $item['cover_photo_data']):?>
				<div class="album-thumb">
					<a href="<?php echo $link;?>">
						<img src="<?php echo $thumb['picture'];?>"/>
					</a>
				</div>
			<?php endif; ?>
			<div class="album-info">
				<h5><a href="<?php echo $link;?>"><?php echo $item['name'];?></a></h5>
				<div class="counts">
					<div class="photos-count"><?php echo $item['count'];?></div>
					<?php if (!empty($thumb['comments'])) :?>
						<div class="comments-count">Comments: <?php echo count($thumb['comments']['data']);?></div>
					<?php endif;?>
					<?php if (!empty($thumb['likes'])) :?>
						<div class="likes-count">Likes: <?php echo count($thumb['likes']['data']);?></div>
					<?php endif;?>
				</div>
			</div>
		</li>
	<?php endforeach; endif;?>
</ol>
<?php
}


/**
 * Render the photo list
 *
 * @param Integer
 */
function theme_show_photos($id) {
	global $paged;

	$per_page = 30;
	if (empty($paged)) $paged = 1;

	// Album Information
	if (!$album = facebook_page_albums_get_album($id)) {
		echo 'failed to get album information';
		return false;
	}


	//
	// Photo List
	//
	if (!$list = facebook_page_albums_get_photo_list($id, array(
		'per_page' => $per_page,
		'paged'    => $paged
	))) {
		echo 'failed to get photo list';
		return;
	}
	?>
	<div class="photo-list-header">
		<h4><a href="<?php echo $album['link'];?>" target="_blank"><?php echo $album['name'];?></a></h4>
		<div class="counts">
			<?php if (!empty($album['count'])) :?>
				<div class="photos-count">Number of Photos: <?php echo $album['count'];?></div>
			<?php endif;?>
			<?php if (!empty($album['comments'])) :?>
				<div class="comments-count">Comments: <?php echo count($album['comments']['data']);?></div>
			<?php endif;?>
			<?php if (!empty($album['likes'])) :?>
				<div class="likes-count">Likes: <?php echo count($album['likes']['data']);?></div>
			<?php endif;?>
		</div>
		<a class="goto-facebook" href="<?php echo $album['link'];?>" target="_blank" title="See on Facebook">See on Facebook</a>
	</div>
	<ol class="photo-list">
		<?php if (!empty($list)): foreach ($list as $item):?>
			<?php
			// It has some images of different sizes.
			// var_dump($item);
			$thumbnail = $item['images'][5];?>
			<li class="photo">
				<div class="photo-thumb">
					<a class="photo-link"
					   href="<?php echo $item['source'];?>"
					   title="<?php if (!empty($item['name'])) echo $item['name'];?>">
						<img src="<?php echo $thumbnail['source'];?>"/>
					</a>
				</div>
				<div class="photo-info">
					<div class="counts">
						<?php if (!empty($item['comments'])) :?>
							<div class="comments-count">Comments: <?php echo count($item['comments']['data']);?></div>
						<?php endif;?>
						<?php if (!empty($item['likes'])) :?>
							<div class="likes-count">Likes: <?php echo count($item['likes']['data']);?></div>
						<?php endif;?>
					</div>
				</div>
			</li>
		<?php endforeach; endif;?>
	</ol>
<?php
	//
	// Page Controller
	//
	echo paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'total' => ceil($album['count'] / $per_page),
		'current' => $paged,
		'prev_text' => '&laquo;',
		'next_text' => '&raquo;',
		'mid_size' => 5
	));
}

