<?php

namespace QTH\SiteBuilder;

class host_info extends base_plugin
{
    public function __construct()
    {
        parent::__construct( 'Host Info', 'host_info' );

        $this->register_all();
    }

    public function get_qth_server_name()
    {
        $local_key_name = 'qth_server_name';

        $hostname = $this->cache_get( $local_key_name );

        if( ! $hostname )
        {
            $hostname = $this->get_qth_server_name_from_env();

            if( ! $hostname )
            {
                $hostname = $this->get_qth_server_name_from_phpinfo();
            }

            if( $hostname )
            {
                $this->cache_set( $local_key_name, $hostname );
            }
        }

        if( ! $hostname )
        {
            $hostname = 'Could not determine server&rsquo;s host';
        }

        return $hostname;
    }

    public function try_get_server_number_from_string( $value )
    {
        $parts = explode( '.', $value );
        if( 3 === count( $parts ) && 'website-server' === $parts[ 1 ] && 'net' === $parts[ 2 ] )
        {
            return (int) str_replace( 'www', '', $parts[ 0 ] );
        }

        return false;
    }

    public function convert_server_number_to_string( $server_number )
    {
        return 'QTH Server #' . $server_number;
    }

    public function get_qth_server_name_from_phpinfo()
    {
        //TODO: This call might be expensive and we should probably staff this
        //in a transient
        ob_start();
        phpinfo( INFO_GENERAL );
        $subject = ob_get_clean();

        $pattern = preg_quote( '<tr><td class="e">System </td><td class="v">' ) .
                    '(?<value>[^<]*)' .
                    preg_quote( '</td></tr>' );
        $count = preg_match( '~' . $pattern . '~', $subject, $matches );

        if( $count && array_key_exists( 'value', $matches ) )
        {
            foreach( explode( ' ', $matches[ 'value' ] ) as $string )
            {
                $server_number = $this->try_get_server_number_from_string( $string );
                if( $server_number )
                {
                    $hostname = $this->convert_server_number_to_string( $server_number );
                    return $hostname;
                }
            }
        }

        return false;

        // return false;

        // dump( $matches );
        // die;
    }

    public function get_qth_server_name_from_env()
    {

        $hostname = $this->get_hostname();

        if( $hostname )
        {
            $server_number = $this->try_get_server_number_from_string( $hostname );
            if( $server_number )
            {
                $hostname = $this->convert_server_number_to_string( $server_number );
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
