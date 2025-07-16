<?php

/**
 * MPDA Consent Management
 *
 * @package MPDA_Consent
 */

namespace Zippy_Core\Src\Admin;

defined('ABSPATH') or die();

use Zippy_Core\Utils\Zippy_Utils_Core;
use WP_REST_Response;
use WP_REST_Request;

class Zippy_Admin_Setting
{
  protected static $_instance = null;

  /**
   * @return Zippy_Admin_Setting
   */

  public static function get_instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct()
  {
    add_action("init", array($this, 'update_search_engine_indexing'));
    add_action('admin_menu',  array($this, 'zippy_setting'));
    add_action('rest_api_init', array($this, 'zippy_setting_init_api'));
    add_filter('plugin_action_links_' . ZIPPY_CORE_BASENAME, array($this, 'zippy_action_links'));
    add_filter('default_content', array($this, 'disable_comments_by_default'), 10, 2);
    add_action('admin_menu', array($this, 'remove_comments_menu'), 20);
    register_activation_hook(__FILE__, array($this, 'disable_comments_on_activation'));
  }

  function zippy_action_links($links)
  {
    $links[] = '<a href="' . menu_page_url('zippy-setting', false) . '">Settings</a>';
    return $links;
  }

  public function zippy_setting()
  {
    add_options_page('Zippy Settings', 'Zippy Settings', 'manage_options', 'zippy-setting', array($this, 'render'), 1);
  }

  public function zippy_setting_init_api()
  {
    register_rest_route('zippy-core/v1', '/check_option', array(
      'methods' => 'GET',
      'callback' => array($this, 'check_option'),
      'permission_callback' => function () {
        return true;
      }
    ));

    register_rest_route('zippy-core/v1', '/update_settings', array(
      'methods' => 'POST',
      'callback' => array($this, 'update_settings'),
      'permission_callback' => function () {
        return true;
      }
    ));
  }

  public function check_option(WP_REST_Request $request)
  {
    // check Authentication;
    $params = $request->get_params();
    $response = array(
      'status' => 'success',
      'message' => 'unauthorized',
    );
    if (!isset($params)) return new WP_REST_Response($response, 400);

    $is_authenticated =  get_option($params['key']);

    if (!empty($is_authenticated) && isset($is_authenticated)) {
      $response = array(
        'status' => 'success',
        'message' => 'autheticated',
      );
    }


    return new WP_REST_Response($response, 200);
  }

  public function update_settings(WP_REST_Request $request)
  {
    // check Authentication;
    $params = $request->get_params();
    $response = array(
      'status' => 'success',
      'message' => 'unauthorized',
    );
    if (!isset($params)) return new WP_REST_Response($response, 400);

    update_option($params['key'], $params['value']);

    $response = array(
      'status' => 'success',
      'message' => 'autheticated',
    );

    return new WP_REST_Response($response, 200);
  }

  public function render()
  {
    echo Zippy_Utils_Core::get_template('admin-settings.php', [], dirname(__FILE__), '/templates');
  }

  // turn off comment for new post
  public function disable_comments_by_default($content, $post) {
    if ($post->post_type === 'post') {
        $post->comment_status = 'closed';
    }
    return $content;
  }

  // Remove Comment Menu
  public function remove_comments_menu() {
    remove_menu_page('edit-comments.php');
  }

  // Disable comment on current posts
  public function disable_comments_on_activation() {
    global $wpdb;
    $wpdb->query("UPDATE $wpdb->posts SET comment_status = 'closed', ping_status = 'closed' WHERE post_type = 'post'");
  }

  // Disable search engine indexing
  public function update_search_engine_indexing() {
    $current_domain = parse_url(home_url(), PHP_URL_HOST);

    // localhost
    if (preg_match('/^localhost(:\d+)?$/', $current_domain)) {
        return; 
    }

    // theshin.info
    if (preg_match('/^(.*\.)?theshin\.info$/', $current_domain)) {
        return;
    }

    // Turn off if another domain
    update_option('blog_public', 1);
  }
}
