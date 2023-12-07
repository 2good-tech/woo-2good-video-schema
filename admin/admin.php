<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class TooGoodVideoSchema {

    public function __construct() {
        add_action('admin_menu', array($this, 'create_2good_video_schema_menu'));
        add_action('admin_init', array($this, 'init_2good_video_schema_settings'));
    }

    function create_2good_video_schema_menu() {
        
        add_menu_page(
            '2GOOD Tech ltd',   // Page title
            '2GOOD Video Schema',   // Menu title
            'manage_options',       // Capability
            '2good_video_schema_menu', // Menu slug
            array($this, 'page_2good_video_schema_menu') 
        );
        add_action('admin_enqueue_scripts', array($this, 'enqueue_2good_video_scripts'));
    }
    
    public function enqueue_2good_video_scripts() {
        wp_enqueue_style('2good_video_style', plugin_dir_url(__FILE__) . 'assets/admin-style.css');
        wp_enqueue_script('2good_video_script', plugin_dir_url(__FILE__) . 'assets/admin-script.js', array(), false, true);
    }

    public function page_2good_video_schema_menu() {
       
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('2good_vs');
                do_settings_sections('2good_vs');
                submit_button(__('Save Settings', '2gvs'));
                ?>
            </form>
        </div>
        <?php
    }

    public function init_2good_video_schema_settings() {
        
        register_setting('2good_vs', '2good_vs_api_key');

        add_settings_section(
            '2good_vs_section',
            __('Settings', '2gvs'),
            null,
            '2good_vs'
        );

        add_settings_field(
            '2good_vs_api_key',
            __('YOUTUBE API KEY', '2gvs'),
            array($this, 'youtube_api_key_field_callback'),
            '2good_vs',
            '2good_vs_section'
        );
    }

    public function youtube_api_key_field_callback() {
        $youtube_api_key = get_option('2good_vs_api_key');
        echo '<input type="text" name="2good_vs_api_key" value="' . esc_attr($youtube_api_key) . '"/>';
    }
}

// Instantiate the main class
$tooGoodVideoSchema = new TooGoodVideoSchema();