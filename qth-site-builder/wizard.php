<?php

namespace QTH\SiteBuilder;

class wizard extends base_plugin
{
    public function __construct()
    {
        parent::__construct( 'Wizard', 'wizard' );

        $this->register_all();
    }

    public function register_all()
    {
        add_action( 'wp_dashboard_setup', array( $this, 'handle_wp_dashboard_setup' ) );
    }

    public function handle_wp_dashboard_setup()
    {
        $widget_slug = $this->get_css_class_name( 'dashboard-widget' );

        wp_add_dashboard_widget( $widget_slug, 'QTH Site Builder', array( $this, 'handle_wp_add_dashboard_widget_wizard' ) );

        //Move our widget to the top, from:
        //https://codex.wordpress.org/Dashboard_Widgets_API

        // Globalize the metaboxes array, this holds all the widgets for wp-admin
        global $wp_meta_boxes;

        // Get the regular dashboard widgets array
        // (which has our new widget already but at the end)
        $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

        // Backup and delete our new dashboard widget from the end of the array
        $example_widget_backup = array( $widget_slug => $normal_dashboard[ $widget_slug ] );
        unset( $normal_dashboard[ $widget_slug ] );

        // Merge the two arrays together so our widget is at the beginning
        $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );

        // Save the sorted array back into the original metaboxes
        $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
    }

    public function handle_wp_add_dashboard_widget_wizard(  $post, $callback_args )
    {
        echo '<a href="#">Launch QTH Site Builder</a>';
    }
}
