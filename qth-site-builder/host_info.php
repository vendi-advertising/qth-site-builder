<?php

namespace QTH\SiteBuilder;

class host_info extends base_plugin
{
    public function __construct()
    {
        parent::__construct( 'Host Info', 'host-info' );

        $this->register_all();
    }

    public function get_qth_server_name()
    {
        $local_key_name = 'qth_server_name';

        $hostname = $this->cache_get( $local_key_name );
        if( ! $value )
        {
            $hostname = $this->get_hostname();

            if( $hostname )
            {
                $parts = explode( '.', $hostname );
                if( 3 === count( $parts ) && 'website-server' === $parts[ 1 ] && 'net' === $parts[ 2 ] )
                {
                    $hostname = 'QTH Server #' . str_replace( 'www', '', $parts[ 0 ] );
                }

                $this->cache_set( $local_key_name, $hostname );
            }
            else
            {
                $hostname = 'Could not determine server&rsquo;s host';
            }
        }

        return $hostname;
    }

    public function register_all()
    {
        add_action( 'admin_bar_menu',   array( $this, 'handle_admin_bar_menu' ), 999, 1 );
        add_action( 'admin_head',       array( $this, 'handle_admin_head' ) );
    }

    public function handle_admin_head()
    {
        echo sprintf(
                        '
                        <style>
                            .%1$s
                            {
                                background-color: rgba( 255, 255, 255, 0.2 ) !important;
                            }
                        </style>
                        ',
                        $this->get_css_class_name( 'admin-bar' )
                    );
    }

    public function get_hostname()
    {
        return getenv( 'HOSTNAME' );
    }

    public function handle_admin_bar_menu( $wp_admin_bar )
    {
        $args = array(
            'id'    => 'my_page',
            'title' => $this->get_qth_server_name(),
            'meta'  => array( 'class' => $this->get_css_class_name( 'admin-bar' ) )
        );
        $wp_admin_bar->add_node( $args );
    }
}
