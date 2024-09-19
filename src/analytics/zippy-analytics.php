<?php

/**
 * Analytics Dashboard
 *
 * @package Shin
 */

namespace Zippy_Core\Src\Analytics;

defined('ABSPATH') or die();

class Zippy_Analytics
{

  protected static $_instance = null;

  /**
   * @return Zippy_Analytics
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
    // $screen = get_current_screen();

    // $page_id = 'woocommerce_page_admin?page=wc-zippy-dashboard';

    // if ($screen->id || $page_id) return;

    add_action('admin_enqueue_scripts', array($this, 'analytics_assets'));

    // add_filter('woocommerce_analytics_report_menu_items', array($this, 'analytics_menu'));

    add_action('admin_menu',  array($this, 'zippy_dashboard'));
  }

  /**
   *
   * Assests Resource
   */

  public function analytics_assets()
  {
    $version = time();
    wp_enqueue_script('chart-js', ZIPPY_CORE_URL . '/assets/dist/js/main.min.js', [], $version, true);
    wp_enqueue_style('zippy-css', ZIPPY_CORE_URL . '/assets/dist/css/main.min.css', [], $version);
  }


  public function analytics_menu($report_pages)
  {
    $shin['woocommerce-analytics-dashboard'] = array(
      'id' => 'woocommerce-analytics-dashboard',
      'title' => __('Dashboard2', 'woocommerce-admin'),
      'parent' => 'woocommerce-analytics',
      'path' => '/analytics/dashboard-shin',
    );
    array_splice($report_pages, 2, 0, $shin);
    return $report_pages;
  }

  public function zippy_dashboard($reports)
  {
    add_submenu_page('woocommerce', 'Dashboard', 'Dashboard', 'manage_options', 'admin.php?page=wc-zippy-dashboard', array($this, 'render'), 1);
  }

  public function render()
  {
    echo  '<link as="style" rel="stylesheet preload prefetch"  href="/wp-content/plugins/woocommerce/assets/client/admin/app/style.css?ver=7.9.0" as="style" />';
    echo '<div id="zippy-root"></div>';
  }
}
