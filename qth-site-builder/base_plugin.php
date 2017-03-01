<?php

namespace QTH\SiteBuilder;

abstract class base_plugin
{
    protected $_plugin_name;

    protected $_plugin_slug;

    protected $_object_cache_group_name = 'qth-site-builder';

    protected $_mu_plugin_slug = 'qth-site-builder';

    public function get_plugin_name()
    {
        return $this->_plugin_name;
    }

    public function set_plugin_name( $value )
    {
        $this->_plugin_name = $value;
    }

    public function get_plugin_slug()
    {
        return $this->_plugin_slug;
    }

    public function set_plugin_slug( $value )
    {
        $this->_plugin_slug = $value;
    }

    public function get_mu_plugin_slug()
    {
        return $this->_mu_plugin_slug;
    }

    public function set_mu_plugin_slug( $value )
    {
        $this->_mu_plugin_slug = $value;
    }

    public function get_object_cache_group_name()
    {
        return $this->_object_cache_group_name;
    }

    public function set_object_cache_group_name( $value )
    {
        $this->_object_cache_group_name = $value;
    }

    public function __construct( $plugin_name, $plugin_slug )
    {
        $this->set_plugin_name( $plugin_name );
        $this->set_plugin_slug( $plugin_slug );
    }

    public final function cache_get( $key, $default = null )
    {
        $ret = wp_cache_get( $key, $this->get_object_cache_group_name(), false, $found = null );
        if( false === $found )
        {
            return $default;
        }

        return $ret;
    }

    public final function cache_set( $key, $data, $expire = 0 )
    {
        return wp_cache_set( $key, $data, $this->get_object_cache_group_name(), $expire );
    }

    public function get_css_class_name( $local_class_name )
    {
        return sprintf( '%1$s-%2$s-%3$s', $this->get_mu_plugin_slug(), $this->get_plugin_slug(), $local_class_name );
    }
}
