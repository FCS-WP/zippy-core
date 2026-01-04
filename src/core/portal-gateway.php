<?php

namespace Zippy_Core\Src\Core;

use WP_Error;

/**
 * Class Portal_Gatewaye
 * 
 */
class Portal_Gateway
{


  const API_NAMESPACE = 'portal/v1';

  public function __construct()
  {
    add_action('rest_api_init', [$this, 'register_routes']);
  }

  /**
   * Note: Define the endpoints and their requirements.
   */
  public function register_routes()
  {
    register_rest_route(self::get_namespace(), '/auth/sso', [
      'methods'  => 'GET',
      'callback' => [$this, 'handle_sso'],
      'permission_callback' => [$this, 'validate_signature'],
    ]);

    // Note: Endpoint to fetch site data (Plugins, Themes, Versions)
    register_rest_route(self::get_namespace(), '/site-info', [
      'methods'  => 'GET',
      'callback' => [$this, 'get_site_info'],
      'permission_callback' => [$this, 'validate_signature'],
    ]);
  }

  /**
   * Note: Security Layer - Validates the HMAC signature for every request.
   */

  public function validate_signature($request)
  {
    $app_key   = $request->get_param('portal_key');
    $timestamp = (int) $request->get_param('portal_ts');
    $signature = $request->get_param('portal_sig');

    if (!$app_key || !$timestamp || !$signature) {
      return new WP_Error('missing_auth', 'Missing authentication parameters', ['status' => 401]);
    }

    if (trim($app_key) !== trim(get_option('wp_portal_app_key'))) {
      return new WP_Error('invalid_key', 'Invalid App Key', ['status' => 401]);
    }

    if (abs(current_time('timestamp', true) - $timestamp) > 300) {
      return new WP_Error('expired', 'Request expired', ['status' => 403]);
    }

    $body = $request->get_body();

    if (empty($body) && $request->get_param('token')) {
      $body = json_encode(['token' => $request->get_param('token')]);
    } elseif (empty($body)) {
      $body = json_encode([]);
    }

    $payload = $timestamp . '.' . $body;
    $expected_signature = hash_hmac('sha256', $payload, trim(get_option('wp_portal_app_secret')));

    if (!hash_equals($expected_signature, (string)$signature)) {
      return new WP_Error('invalid_sig', 'Signature mismatch', ['status' => 403]);
    }

    return true;
  }

  /**
   * Note: Logic for SSO Login
   */
  public function handle_sso($request)
  {
    $token   = $request->get_param('token');
    $app_key = trim(get_option('wp_portal_app_key'));

    $portal_url = defined('PORTAL_INTERNAL_URL') ? PORTAL_INTERNAL_URL : 'http://localhost:8000';

    $response = wp_remote_post($portal_url . '/api/v1/verify-sso-token', [
      'headers' => [
        'Accept' => 'application/json',
      ],
      'body' => [
        'token'   => $token,
        'app_key' => $app_key,
      ],
      'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
      wp_die('Portal Connection Error: ' . $response->get_error_message());
    }

    $body = json_decode(wp_remote_retrieve_body($response));


    if (isset($body->valid) && $body->valid === true) {
      $users = get_users(['role' => 'administrator', 'number' => 1]);
      $admin = !empty($users) ? $users[0] : null;

      if ($admin) {
        wp_logout();

        wp_set_current_user($admin->ID);
        wp_set_auth_cookie($admin->ID, true); // Note: Set 'Remember Me' to true

        wp_safe_redirect(admin_url());
        exit;
      }
    }

    wp_die('SSO Authentication Failed: Invalid or Expired Token.', 'Unauthorized', ['response' => 403]);
  }

  /**
   * Note: Collects all relevant site data for the Portal dashboard.
   */
  public function get_site_info()
  {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    return rest_ensure_response([
      'wp_version' => get_bloginfo('version'),
      'php_version' => phpversion(),
      'active_plugins' => get_option('active_plugins'),
      'all_plugins' => array_keys(get_plugins()),
      'theme' => get_stylesheet(),
    ]);
  }

  public static function get_namespace()
  {
    return defined('PORTAL_CUSTOM_NAMESPACE') ? PORTAL_CUSTOM_NAMESPACE : self::API_NAMESPACE;
  }
}

