<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once plugin_dir_path(__FILE__) . '../includes/main.php';

class TooGoodVideoSchema {

    public function __construct() {
        add_action('admin_menu', array($this, 'create_2good_video_schema_menu'));
        add_action('admin_init', array($this, 'init_2good_video_schema_settings'));
        add_action('wp_ajax_parse_all_schemas', array($this, 'parse_all_schemas'));
        add_action('wp_ajax_test_youtube_api_key', array($this, 'test_youtube_api_key'));
    }

    function create_2good_video_schema_menu() {
        
        add_menu_page(
            '2GOOD Video Schema',   // Page title
            'Video Schema',   // Menu title
            'manage_options',       // Capability
            '2good_video_schema_menu', // Menu slug
            array($this, 'page_2good_video_schema_menu'),
            //'dashicons-embed-video' // Icon
            '' // Icon
        );
        add_action('admin_enqueue_scripts', array($this, 'enqueue_2good_video_scripts'));
    }
    
    public function enqueue_2good_video_scripts() {
        //load only when in the plugin admin page
        if (get_current_screen()->id !== 'toplevel_page_2good_video_schema_menu') {
            return;
        } else {
            wp_enqueue_style('2good_video_style', plugin_dir_url(__FILE__) . 'assets/admin-style.css');
            wp_enqueue_script('2good_video_script', plugin_dir_url(__FILE__) . 'assets/admin-script.js', array(), false, true);
        }
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
                submit_button(__('Save Key', '2gvs'));
                ?>
            </form>
            <hr/>
            <h2>Actions</h2>
            <div class="button-actions">  
                <button type="button" id="test_youtube_api_key_button" class="button button-primary">Test YouTube API Key</button>
                <div id="test_youtube_api_key_result" class="fa"></div>
            </div>
            <div class="button-actions"> 
                <button type="button" id="parse_all_schemas" class="button button-primary">Parse all videos</button>
                <div id="parse_all_schemas_result"></div>
            </div>
        </div>
        <?php
    }
	
    public function youtube_api_key_field_callback() {
        $youtube_api_key = get_option('2good_vs_api_key');
        ?>
        <input type="text" name="2good_vs_api_key" value="<?php echo esc_attr($youtube_api_key); ?>"/>
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
 
    public function test_youtube_api_key() {
        $youtube_api_key = get_option('2good_vs_api_key');
    
        $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=1&q=test&key=' . $youtube_api_key;
    
        $response = wp_remote_get($url);
    
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            echo 'API request failed: ' . $error_message;
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            exit(json_encode($response_code));
        }
    }    

    function get_video_schema_count() {
        global $wpdb; // WordPress database global variable
    
        $query = "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = '2good_video_schemas'";
        $count = $wpdb->get_var($query);
    
        return $count;
    }
    

    function parse_helper() {
        $args = array(
            'post_type'      => array('post', 'page', 'product'),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        
        $all_posts = get_posts($args);
        
        foreach ($all_posts as $post) {
            parse_all_from_Gut_admin($post->ID);
            parse_all_from_Ele_admin($post->ID);
        }
        
    }
    
    public function parse_all_schemas() {
        $this->parse_helper();
        
        $schemas_count = $this->get_video_schema_count();
        echo json_encode(['success' => true, 'message' => 'Parsing completed.', 'count' => $schemas_count]);
        wp_die();
    }
    
    
}

// Instantiate the main class
$tooGoodVideoSchema = new TooGoodVideoSchema();