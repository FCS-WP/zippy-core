<?php

/**
 * Optimise theme
 *
 * @package Shin
 */

namespace Zippy_Core\Src\Core;

defined('ABSPATH') or die();

class Zippy_Optimise
{

	public function __construct()
	{
		//load all class in here
		$this->set_hooks();
	}

	protected function set_hooks()
	{
		add_filter('script_loader_tag', [$this, 'add_defer_attribute'], 10, 2);

		add_filter('script_loader_tag', [$this, 'add_async_attribute'], 10, 2);

		add_action('wp_enqueue_scripts', [$this, 'remove_block_css'], 100);


		remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
		remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

		// Disable Comments
		add_filter('comments_open', '__return_false', 20, 2);
		add_filter('pings_open', '__return_false', 20, 2);
		add_filter('comments_array', '__return_empty_array', 10, 2);
		add_action('admin_menu', [$this, 'disable_comments_admin_menu']);
		add_action('admin_init', [$this, 'disable_comments_admin_menu_redirect']);
		add_action('wp_before_admin_bar_render', [$this, 'disable_comments_admin_bar']);
		add_action('init', [$this, 'disable_comments_support']);
	}


	public function add_defer_attribute($tag, $handle)
	{
		// add script handles to the array below
		$scripts_to_defer = array('main-scripts-js', '');

		foreach ($scripts_to_defer as $defer_script) {
			if ($defer_script === $handle) {
				return str_replace(' src', ' defer src', $tag);
			}
		}

		return $tag;
	}

	public function add_async_attribute($tag, $handle)
	{
		// add script handles to the array below
		$scripts_to_async = array('formidable-js', '');

		foreach ($scripts_to_async as $async_script) {
			if ($async_script === $handle) {
				return str_replace(' src', ' async src', $tag);
			}
		}

		return $tag;
	}


	public function remove_block_css()
	{
		wp_dequeue_style('wp-block-library'); // Wordpress core
		wp_dequeue_style('wp-block-library-theme'); // Wordpress core
		wp_dequeue_style('wc-block-style'); // WooCommerce
		wp_dequeue_style('storefront-gutenberg-blocks'); // Storefront theme
	}

	public function disable_comments_admin_menu()
	{
		remove_menu_page('edit-comments.php');
	}

	public function disable_comments_admin_menu_redirect()
	{
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_safe_redirect(admin_url());
			exit;
		}
	}

	public function disable_comments_admin_bar()
	{
		global $wp_admin_bar;
		if ($wp_admin_bar) {
			$wp_admin_bar->remove_menu('comments');
		}
	}

	public function disable_comments_support()
	{
		$post_types = get_post_types();
		foreach ($post_types as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}
	}
}
