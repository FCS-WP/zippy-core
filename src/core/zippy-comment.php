<?php

/**
 * Admin Setting
 *
 * @package Shin
 */

namespace Zippy_Core\Src\Core;

defined('ABSPATH') or die();

class Zippy_Comment
{

	public function __construct()
	{
		//load all class in here
		$this->set_hooks();
	}

	protected function set_hooks()
	{
		add_action('admin_init', array($this, "disable_comments"));
		add_filter('comments_open', '__return_false', 20, 2);
		add_filter('pings_open', '__return_false', 20, 2);
    	add_filter('default_content', array($this, 'disable_comments_by_default'), 10, 2);
	}

	// turn off comment for new post
	public function disable_comments_by_default($content, $post) {
		if ($post->post_type === 'post') {
			$post->comment_status = 'closed';
		}
		return $content;
	}

	public function disable_comments(){

		// Disable comment on current existing posts
		global $wpdb;
		$wpdb->query("UPDATE $wpdb->posts SET comment_status = 'closed', ping_status = 'closed' WHERE post_type = 'post'");

		// Remove Comment Menu
		remove_menu_page('edit-comments.php');

		// Redirect any user trying to access comments page
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url());
			exit;
		}

		// Remove comments metabox from dashboard
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

		// Disable support for comments and trackbacks in post types
		foreach (get_post_types() as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
			}
		}
	}
}
