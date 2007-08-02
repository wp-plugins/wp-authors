<?php
/*
Plugin Name: WP Authors
Plugin URI: http://www.tsaiberspace.net/projects/wordpress/wp-authors/
Description: Sidebar widget to list all authors of a blog. Navigate to <a href="widgets.php">Presentation &rarr; Widgets</a> to add to your sidebar.
Author: Robert Tsai
Author URI: http://www.tsaiberspace.net/
Version: 1.0
*/

function widget_wpauthors_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;

	function wp_widget_authors($args) {
		extract($args);

		$options = get_option('widget_authors');
		$c = $options['count'] ? '1' : '0';
		$f = $options['show_fullname'] ? '1' : '0';
		$hide = $options['hide_empty'] ? '1' : '0';
		$excludeadmin = $options['exclude_admin'] ? '1' : '0';
		$title = empty($options['title']) ? __('Authors') : $options['title'];

		$author_args = "orderby=name&optioncount={$c}&show_fullname={$f}&hide_empty={$hide}&exclude_admin={$excludeadmin}&post_types=post";

		print <<<EOM
		$before_widget
		<ul>
		$before_title$title$after_title
EOM;

		wp_list_authors($author_args . '&title_li=');

		print <<<EOM
		</ul>
		$after_widget
EOM;
	}

	function wp_widget_authors_control() {
		$options = $newoptions = get_option('widget_authors');
		if ( !is_array($options) )
			$options = array(
				'title' => 'Authors',
				'count' => true,
				'show_fullname' => false,
				'hide_empty' => true,
				'exclude_admin' => true,
				);
		if ( $_POST['authors-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['authors-title']));
			$newoptions['count'] = isset($_POST['authors-count']);
			$newoptions['show_fullname'] = isset($_POST['authors-show_fullname']);
			$newoptions['hide_empty'] = isset($_POST['authors-hide_empty']);
			$newoptions['exclude_admin'] = isset($_POST['authors-exclude_admin']);
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_authors', $options);
		}
		$title = attribute_escape($options['title']);
		$count = $options['count'] ? 'checked="checked"' : '';
		$show_fullname = $options['show_fullname'] ? 'checked="checked"' : '';
		$show_empty = !$options['hide_empty'] ? 'checked="checked"' : '';
		$show_admin = !$options['exclude_admin'] ? 'checked="checked"' : '';
?>

						<p><label for="authors-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="authors-title" name="authors-title" type="text" value="<?php echo $title; ?>" /></label></p>
						<p style="text-align:right;margin-right:40px;"><label for="authors-count"><?php _e('Show post counts'); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="authors-count" name="authors-count" /></label></p>
						<p style="text-align:right;margin-right:40px;"><label for="authors-show_fullname"><?php _e('Show full names'); ?> <input class="checkbox" type="checkbox" <?php echo $show_fullname; ?> id="authors-show_fullname" name="authors-show_fullname" /></label></p>
						<p style="text-align:right;margin-right:40px;"><label for="authors-hide_empty"><?php _e('Hide empty authors'); ?> <input class="checkbox" type="checkbox" <?php echo $show_empty; ?> id="authors-hide_empty" name="authors-hide_empty" /></label></p>
						<p style="text-align:right;margin-right:40px;"><label for="authors-exclude_admin"><?php _e('Exclude admin'); ?> <input class="checkbox" type="checkbox" <?php echo $show_admin; ?> id="authors-exclude_admin" name="authors-exclude_admin" /></label></p>
						<input type="hidden" id="authors-submit" name="authors-submit" value="1" />
<?php
	}

	register_sidebar_widget('Authors', 'wp_widget_authors');
	register_widget_control('Authors', 'wp_widget_authors_control', 300, 150);
}

function widget_wpauthors_deactivate() {
	delete_option('widget_authors');
}

register_deactivation_hook(__FILE__, 'widget_wpauthors_deactivate');
add_action('plugins_loaded', 'widget_wpauthors_init');

?>
