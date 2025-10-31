<?php

namespace Zippy_Core;

defined('ABSPATH') || exit;

abstract class Core_Module {

    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_required_files']);
        add_action('plugins_loaded', [$this, 'init_module']);
    }

    abstract public function load_required_files();
    abstract public function init_module();
}