<?php

/**
 * Admin Setting
 *
 * @package Shin
 */

namespace Zippy_Core\Src\Woocommerce;

defined('ABSPATH') or die();

class Zippy_Woocommerce
{
  protected static $_instance = null;

  /**
   * @return Zippy_Woocommerce
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
    if (!function_exists('is_plugin_active')) {

      include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    if (!is_plugin_active('woocommerce/woocommerce.php')) return;

    //load all class in here
    $this->set_hooks();
  }

  protected function set_hooks()
  {
    add_filter('wc_get_template_part', array($this, 'override_woocommerce_template_part'), 1, 3);
    add_filter('woocommerce_locate_template', array($this, 'override_woocommerce_template'), 1, 3);
    add_action('woocommerce_admin_order_data_after_order_details', array($this, 'display_pre_order_options'), 10, 1);
    add_action('woocommerce_process_shop_order_meta', array($this, 'update_pre_order_options'), 10, 1);
  }


  /**
   * Template Part's
   *
   * @param  string $template Default template file path.
   * @param  string $slug     Template file slug.
   * @param  string $name     Template file name.
   * @return string           Return the template part from plugin.
   */
  public function override_woocommerce_template_part($template, $slug, $name)
  {

    $template_directory = untrailingslashit(plugin_dir_path(__FILE__)) . "/templates/";
    if ($name) {
      $path = $template_directory . "{$slug}-{$name}.php";
    } else {
      $path = $template_directory . "{$slug}.php";
    }
    return file_exists($path) ? $path : $template;
  }
  /**
   * Template File
   *
   * @param  string $template      Default template file  path.
   * @param  string $template_name Template file name.
   * @param  string $template_path Template file directory file path.
   * @return string                Return the template file from plugin.
   */
  public function override_woocommerce_template($template, $template_name, $template_path)
  {

    $template_directory = untrailingslashit(plugin_dir_path(__FILE__)) . "/templates/";

    $path = $template_directory . $template_name;
    // echo 'template: ' . $path . '<br/>';

    return file_exists($path) ? $path : $template;
  }

  public function display_pre_order_options($order)
  {
    $order_id = $order->get_id() ?? 0;

    echo '<div 
        id="zippy-pre-order-options"
        data-order-id="' . esc_attr($order_id) . '"
    ></div>';
  }

  public function update_pre_order_options($order_id)
  {
    if (isset($_POST['enable_pre_orders'])) {
      $order = wc_get_order($order_id);

      if ($_POST['enable_pre_orders'] == 'yes') {
        $order->update_meta_data(IS_PRE_ORDER_ORDER, 'yes');
      }

      if ($_POST['enable_pre_orders'] == 'no') {
        $order->delete_meta_data(IS_PRE_ORDER_ORDER);
      }

      $order->save();
    }
  }
}
