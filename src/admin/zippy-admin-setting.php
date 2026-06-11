<?php

/**
 * MPDA Consent Management
 *
 * @package MPDA_Consent
 */

namespace Zippy_Core\Src\Admin;

defined('ABSPATH') or die();

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
  }
}
