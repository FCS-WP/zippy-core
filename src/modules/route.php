<?php

namespace Zippy_Core;

abstract class Core_Route {
    protected static $_instance = null;
    
    /**
     * 
     * @return Core_Route
     */

    public static function get_instance() {
        if ( is_null( static::$_instance ) ) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * Auto run & init function
     * @return void;
     */

     public function __construct() {
        add_action( 'rest_api_init', [ $this, 'orders_init_api' ] );
    }


     /**
     * Each child class should define its own API routes here.
     *
     * @return void
     */
    abstract public function orders_init_api();
}