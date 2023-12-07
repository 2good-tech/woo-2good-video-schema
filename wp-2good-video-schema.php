<?php
/*

    Plugin Name: 2GOOD Video Schema
    Description: Adds 2GOOD schema markup to Video blocks/elements created via Elementor or Gutenberg(other editors might need adjustments).
    Version: 1.0.0
    Author: 2GOOD Tech LTD
    License: GPLv2 or later
    License URI: http://www.gnu.org/licenses/gpl-2.0.html
    
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
};

define('TWOGOOD_VIDEO_SCHEMA_DIR', plugin_dir_path(__FILE__));

require_once (TWOGOOD_VIDEO_SCHEMA_DIR . 'admin/admin.php');
include_once(TWOGOOD_VIDEO_SCHEMA_DIR . 'init.php');

add_action('admin_init', 'load_2good_video_schema');

function load_2good_video_schema() {
    global $pagenow;

    if (is_admin()) {
        include_once(TWOGOOD_VIDEO_SCHEMA_DIR . 'includes/main.php');
    }
}