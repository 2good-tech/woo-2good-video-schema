<?php
/*
 * Plugin Name: 2GOOD Video Schema
 * Description: Adds video schema markup to posts with Video blocks/elements created via Elementor or Gutenburg.
 * Version: 1.0.2
 * Author: 2GOOD Technologies Ltd.
 * Author URI: https://2good.tech
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *  
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
};

define('TWOGOOD_VIDEO_SCHEMA_DIR', plugin_dir_path(__FILE__));

include_once(TWOGOOD_VIDEO_SCHEMA_DIR . 'init.php');

if (is_admin()) {
    require_once(TWOGOOD_VIDEO_SCHEMA_DIR . 'admin/admin.php');
    add_action('admin_init', 'load_2good_video_schema');
}

function load_2good_video_schema() {
    global $pagenow;

    if (is_admin()) {
        include_once(TWOGOOD_VIDEO_SCHEMA_DIR . 'includes/main.php');
    }
}