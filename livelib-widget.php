<?php
/*
Plugin Name: LiveLib Widget
Plugin URI: http://uplift.ru/projects/livelib-widget/
Description: Displays the latest books you have read from your LiveLib account.
Version: 1.3-trunk
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
*/

require_once(ABSPATH . WPINC . '/rss.php');

function livelib_display() {
	$options = get_option('widget_livelib');
	$cache_key = md5(serialize($options));
	if ( $output = wp_cache_get($cache_key) )
		return print($output);

	ob_start();

	$profile_url = str_replace('/rss', '/profile', $options['user_rss']);
	$rss = fetch_rss($options['user_rss']);
	if ( isset($rss->items) && 0 != count($rss->items) ) :
?>
<ul id="latestbooks">
<?php
		$rss->items = array_slice($rss->items, 0, $options['items_to_show']);
		foreach ( $rss->items as $item ) :
			$title = wp_specialchars($item['title']);
			preg_match('/^«(.*)» (.*)/u', $item['title'], $title);
			preg_match('/img\/(.+).gif"/', $item['description'], $icons);
			preg_match('/href="([^ ]+)"/', $item['description'], $links);
			preg_match_all('/<a href="([^ ]+)\/tag\/(.+)">(.+)<\/a>/', $item['description'], $tags);
?>
<li class="book <?php echo $icons[1]; ?>">
<div class="title"><a href="<?php echo $links[1]; ?>" rel="nofollow"><?php echo $title[1]; ?></a></div>
<div class="author"><?php echo $title[2]; ?></div>
<div class="tags"><small><?php echo str_replace('-', ' ', implode(', ', $tags[3])); ?></small></div>
</li>
<?php
		endforeach;
?>
<li class="profile"><a href="<?php echo $profile_url; ?>" rel="nofollow"><?php _e('My LiveLib profile', 'livelib'); ?></a></li>
</ul>
<?php
	endif;

	wp_cache_add($cache_key, ob_get_flush());
}

function livelib_widget($arguments) {
	extract($arguments);
	$options = get_option('widget_livelib');

	if ( !is_home() && $options['main_page_only'] )
		return;

	$title = (empty($options['title']) ? __('Recent Reading', 'livelib') : $options['title']);

	echo $before_widget . $before_title . $title . $after_title;
	livelib_display();
	echo $after_widget;
}

function livelib_widget_control() {
	$options = get_option('widget_livelib');

	if ( $_POST['livelib-widget-submit'] ) {
		$options['title'] = strip_tags(stripslashes($_POST['livelib-widget-title']));
		$options['main_page_only'] = isset($_POST['livelib-widget-main-page']);
		update_option('widget_livelib', $options);
	}

	$title = attribute_escape($options['title']);
?>
<p><label for="livelib-widget-title"><?php _e('Title:'); ?>
<input style="width: 250px;" id="livelib-widget-title" name="livelib-widget-title" type="text" value="<?php echo $title; ?>" />
</label></p>

<p><label for="livelib-widget-main-page">
<input type="checkbox" class="checkbox" id="livelib-widget-main-page" name="livelib-widget-main-page"<?php echo $options['main_page_only'] ? ' checked="checked"' : ''; ?> /> <?php _e('Display on main page only', 'livelib'); ?>
</label></p>

<input type="hidden" id="livelib-widget-submit" name="livelib-widget-submit" value="1" />
<?
}

function livelib_add_default_options() {
	$options = get_option('widget_livelib');

	if ( empty($options['user_rss']) )
		$options['user_rss'] = '';

	if ( empty($options['items_to_show']) )
		$options['items_to_show'] = '5';

	if ( empty($options['main_page_only']) )
		$options['main_page_only'] = true;

	add_option('widget_livelib', $options);

	register_uninstall_hook(__FILE__, 'livelib_remove_default_options');
}
register_activation_hook(__FILE__, 'livelib_add_default_options');

function livelib_remove_default_options() {
	delete_option('widget_livelib');
}

function livelib_css() {
	wp_enqueue_style('livelib', plugins_url('style.css', __FILE__), false, '1.1');
}
add_action('init', 'livelib_css');

function livelib_show_options_page() {
	$options = get_option('widget_livelib');
?>
<div class="wrap">
<h2><?php _e('LiveLib Widget Options', 'livelib'); ?></h2>
<form method="post" action="options.php">
<?php settings_fields('livelib_options'); ?>

<table class="form-table">

<tr valign="top">
<th scope="row"><label for="user_rss"><?php _e('Your LiveLib user RSS link:', 'livelib'); ?></label></th>
<td><input type="text" name="widget_livelib[user_rss]" id="user_rss" value="<?php echo $options['user_rss']; ?>" size="50" /><br />
<?php _e('(For example: http://www.livelib.ru/reader/[your_login]/rss)', 'livelib'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><label for="items_to_show"><?php _e('Number of items to show:', 'livelib'); ?></label></th>
<td><input type="text" name="widget_livelib[items_to_show]" id="items_to_show" size="3" value="<?echo $options['items_to_show']; ?>" /></td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'livelib'); ?>" />
</p>

</form>
</div>
<?php
}

function livelib_add_options_page() {
	add_options_page(__('LiveLib Widget', 'livelib'), __('LiveLib Widget', 'livelib'), 'administrator', __FILE__, 'livelib_show_options_page');
}
add_action('admin_menu', 'livelib_add_options_page');

function livelib_register_settings() {
	register_setting('livelib_options', 'widget_livelib');
}
add_action('admin_init', 'livelib_register_settings');

function livelib_register() {
	register_sidebar_widget(__('LiveLib Widget', 'livelib'), 'livelib_widget');
	register_widget_control(__('LiveLib Widget', 'livelib'), 'livelib_widget_control', 300, 100);
}
add_action('widgets_init', 'livelib_register');

function livelib_init() {
	load_plugin_textdomain('livelib', false, dirname(plugin_basename(__FILE__)));
}
add_action('init', 'livelib_init');
?>