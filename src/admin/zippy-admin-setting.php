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
    add_action('admin_menu',  array($this, 'zippy_setting'));
    add_action('rest_api_init', array($this, 'zippy_setting_init_api'));
    add_action('admin_enqueue_scripts', array($this, 'remove_default_stylesheets'));
    add_filter('plugin_action_links_' . ZIPPY_CORE_BASENAME, array($this, 'zippy_action_links'));
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

  public function remove_default_stylesheets($handle)
  {
    $apply_urls = [
      'settings_page_zippy-setting',
      'woocommerce_page_admin-page-wc-zippy-dashboard'
    ];

    if (in_array($handle, $apply_urls)) {
      // Deregister the 'forms' stylesheet
      wp_deregister_style('forms');

      add_action('admin_head', function () {
        $admin_url = get_admin_url();
        $styles_to_load = [
          'dashicons',
          'admin-bar',
          'common',
          'admin-menu',
          'dashboard',
          'list-tables',
          'edit',
          'revisions',
          'media',
          'themes',
          'about',
          'nav-menus',
          'wp-pointer',
          'widgets',
          'site-icon',
          'l10n',
          'buttons',
          'wp-auth-check'
        ];

        $wp_version = get_bloginfo('version');

        // Generate the styles URL
        $styles_url = $admin_url . '/load-styles.php?c=0&dir=ltr&load=' . implode(',', $styles_to_load) . '&ver=' . $wp_version;

        // Enqueue the stylesheet
        echo '<link rel="stylesheet" href="' . esc_url($styles_url) . '" media="all">';
      });
    }
  }
}
