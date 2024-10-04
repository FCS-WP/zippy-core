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
  }

  public function zippy_setting()
  {
    add_options_page('Zippy Settings', 'Zippy Settings', 'manage_options', 'zippy-setting', array($this, 'render'), 1);
  }

  public function zippy_setting_init_api()
  {
    register_rest_route('zippy-core/v1', '/auth_status', array(
      'methods' => 'GET',
      'callback' => array($this, 'check_auth_status'),
      'permission_callback' => function () {
        return true;
      }
    ));
  }

  public function check_auth_status(WP_REST_Request $request)
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

  public function render()
  {
    echo Zippy_Utils_Core::get_template('admin-settings.php', [], dirname(__FILE__), '/templates');
  }
}
