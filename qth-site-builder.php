<?php

/*
Plugin Name: QTH Site Builder
Plugin URI: https://hosting.qth.com
Description: QTH-specific tools for building sites
Author: Vendi Advertising (Chris Haas)
Version: 0.0.1
*/

define( 'QTH_SITE_BUILDER_VERSION',             '0.0.1' );
// define( 'QTH_SITE_BUILDER_PLUGIN_URL',          content_url( 'mu-plugins' ) );
define( 'QTH_SITE_BUILDER_PLUGIN_FILE',         __FILE__ );
define( 'QTH_SITE_BUILDER_PLUGIN_PATH',         dirname( __FILE__ ) );
define( 'QTH_SITE_BUILDER_PLUGIN_FOLDER_NAME', 'qth-site-builder' );

//Register an autoloader for our plugin's actual code
spl_autoload_register(
                        function ( $class )
                        {
                            //PSR-4 compliant autoloader
                            //See http://www.php-fig.org/psr/psr-4/
                            $prefixes = array(
                                                'QTH\\SiteBuilder\\' => QTH_SITE_BUILDER_PLUGIN_PATH . '/' . QTH_SITE_BUILDER_PLUGIN_FOLDER_NAME . '/',
                                            );

                            foreach( $prefixes as $prefix => $base_dir )
                            {
                                // does the class use the namespace prefix?
                                $len = strlen( $prefix );
                                if ( 0 !== strncmp( $prefix, $class, $len ) )
                                {
                                    // no, move to the next registered prefix
                                    continue;
                                }

                                // get the relative class name
                                $relative_class = substr( $class, $len );

                                // replace the namespace prefix with the base directory, replace namespace
                                // separators with directory separators in the relative class name, append
                                // with .php
                                $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

                                // if the file exists, require it
                                if ( file_exists( $file ) )
                                {
                                    require_once $file;
                                }
                            }
                        }
                    );

$plugins = array(
                    'host_info',
                    'wizard',
                );

foreach( $plugins as $plugin )
{
    $name = "\\QTH\\SiteBuilder\\$plugin";
    new $name();
}

//Handle cache-specific login issues
if ( defined( 'WP_CACHE' ) && WP_CACHE )
{
    add_action( 'login_head', 'wp_cache_flush' );
}
