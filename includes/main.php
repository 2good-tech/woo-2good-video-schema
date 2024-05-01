<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
};

include_once 'helpers.php'; 
include_once 'apis.php'; 

function action_save_post_Elementor($post_id, $editor_data) {
    //error_log('Elementor -> POST IS ' . $post_id);
    //error_log('POST Data: ' . print_r($editor_data, true));
    $videoUrls = find_video_widget_Elementor($editor_data);
    $curr_schemas = [];

    if (!empty($videoUrls['youtube']) || !empty($videoUrls['dailymotion']) || !empty($videoUrls['vimeo'])) {
        foreach ($videoUrls as $type => $urls) {
            if (!empty($urls)) {
                foreach ($urls as $url) {
                    $api_res = [];
    
                    switch ($type) {
                        case 'youtube':
                            $youtube_api_key = get_option('2good_vs_api_key');
                            if (empty($youtube_api_key)) {
                                return;
                            }
                            $api_res = get_youtube_video_details($url, $youtube_api_key);
                            break;
                        case 'dailymotion':
                            $api_res = get_dailymotion_video_details($url);
                            break;
                        case 'vimeo':
                            $apikey = '';
                            $api_res = get_vimeo_video_details($url, $apikey);
                            break;
                        default:
                            // Default case handler (if needed)
                            break;
                    }
    
                    if (!empty($api_res)) {
                        $the_schema = generate_video_schema($api_res, $post_id);
                        $curr_schemas[] = $the_schema;
                    }
                }
            }   
        }
        //error_log('All schemas: ' . print_r($curr_schemas, true));

        if (!empty($curr_schemas)) {
            save_seo_video_schemas($post_id, $curr_schemas);
        } else {
            // If there are no video URLs, delete any existing schemas
            delete_post_meta($post_id, '2good_video_schemas');
        }
    } else {
        delete_post_meta($post_id, '2good_video_schemas');

        return;
    }
    
}

function parse_all_from_Ele_admin($post_id) {
    $videoUrls = [
        'youtube' => [],
    ];
    $elementor_data = get_post_meta($post_id, '_elementor_data', true);

    if (!$elementor_data) {
        return;
    }

    if (is_string($elementor_data)) {
        $elementor_data = json_decode($elementor_data, true);
    }

    if (!is_array($elementor_data)) {
        return;
    }

    $videoUrls['youtube'] = find_youtube_urls_in_elementor($elementor_data);

    if (!empty($videoUrls['youtube']) || !empty($videoUrls['dailymotion']) || !empty($videoUrls['vimeo'])) {
        foreach ($videoUrls as $type => $urls) {
            if (!empty($urls)) {
                foreach ($urls as $url) {
                    $api_res = [];
    
                    switch ($type) {
                        case 'youtube':
                            $youtube_api_key = get_option('2good_vs_api_key');
                            if (empty($youtube_api_key)) {
                                return;
                            }
                            $api_res = get_youtube_video_details($url, $youtube_api_key);
                            break;
                        case 'dailymotion':
                            $api_res = get_dailymotion_video_details($url);
                            break;
                        case 'vimeo':
                            $apikey = '';
                            $api_res = get_vimeo_video_details($url, $apikey);
                            break;
                        default:
                            // Default case handler (if needed)
                            break;
                    }
    
                    if (!empty($api_res)) {
                        $the_schema = generate_video_schema($api_res, $post_id);
                        $curr_schemas[] = $the_schema;
                    }
                }
            }   
        }
        //error_log('All schemas: ' . print_r($curr_schemas, true));

        if (!empty($curr_schemas)) {
            save_seo_video_schemas($post_id, $curr_schemas);
        } else {
            // If there are no video URLs, delete any existing schemas
            delete_post_meta($post_id, '2good_video_schemas');
        }
    } else {
        delete_post_meta($post_id, '2good_video_schemas');
        return;
    }
}

function find_youtube_urls_in_elementor($element) {
    $urls = [];

    if (is_array($element)) {
        foreach ($element as $item) {
            if (is_array($item)) {
                // If the item is an array, recursively search within it
                $urls = array_merge($urls, find_youtube_urls_in_elementor($item));
            } elseif (is_string($item) && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\s\"\'<>&]+)/', $item, $matches)) {
                // If the item is a string, check for YouTube URLs
                $urls[] = $matches[1];
            }
        }
    }

    return $urls;
}

function parse_all_from_Gut_admin($post_id) {

    $post_object = get_post($post_id);
        if (!$post_object) {
            //error_log('Gutenberg -> Unable to retrieve post object for post ' . $post_id);
            return;
        }

        // Only proceed if the post is published to avoid parsing DRAFTS!
        if ($post_object->post_status != 'publish') {
            //error_log('Gutenberg -> POST status is :' . $post_object->post_status);
            return;
        }

        $post_content = $post_object->post_content;
        $blocks = parse_blocks($post_content);
        $videoUrls = find_video_block_Gutenberg($blocks);
        //error_log('All schemas: ' . print_r($blocks, true));
        $curr_schemas = [];

        if (!empty($videoUrls['youtube']) || !empty($videoUrls['dailymotion']) || !empty($videoUrls['vimeo'])) {
            foreach ($videoUrls as $type => $urls) {
                if (!empty($urls)) {
                    foreach ($urls as $url) {
                        $api_res = [];

                        switch ($type) {
                            case 'youtube':
                                $youtube_api_key = get_option('2good_vs_api_key');
                                if (empty($youtube_api_key)) {
                                    return;
                                }
                                $api_res = get_youtube_video_details($url, $youtube_api_key);
                                break;
                            case 'dailymotion':
                                $api_res = get_dailymotion_video_details($url);
                                break;
                            case 'vimeo':
                                $apikey = 'YOUR_VIMEO_API_KEY';
                                $api_res = get_vimeo_video_details($url, $apikey);
                                break;
                            default:
                                // Default case handler (if needed)
                                break;
                        }

                        if (!empty($api_res)) {
                            $the_schema = generate_video_schema($api_res, $post_id);
                            $curr_schemas[] = $the_schema;
                        }
                    }
                }   
            }

            //error_log('All schemas: ' . print_r($curr_schemas, true));
        
            if (!empty($curr_schemas)) {
                save_seo_video_schemas($post_id, $curr_schemas);
            } else {
                // If there are no video URLs, delete any existing schemas
                delete_post_meta($post_id, '2good_video_schemas');
            }

        } else {
            delete_post_meta($post_id, '2good_video_schemas');
            return;
        }
}

function action_save_post_Editors($post_id) {

    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return; // Skip processing for autosaves and revisions
    }

    // Retrieve the post object
    $post_object = get_post($post_id);
    if (!$post_object) {
        //error_log('Unable to retrieve post object for post ' . $post_id);
        return;
    }

    // Only proceed if the post is published to avoid parsing DRAFTS!
    if ($post_object->post_status != 'publish') {
        //error_log('Gutenberg -> POST status is :' . $post_object->post_status);
        return;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'elementor_ajax') {
        //error_log('Elementor save detected, skipping Gutenberg processing ' . $post_id);
        return;
    }

    // check if the post is being updated or published with Gutenberg editor
    if ( isset($_POST['action']) && $_POST['action'] == 'editpost' && has_blocks($post_object->post_content) ) {
        //error_log('Gutenberg -> POST IS ' . $post_id);
        $post_content = $post_object->post_content;
        $blocks = parse_blocks($post_content);
        $videoUrls = find_video_block_Gutenberg($blocks);
        //error_log('All schemas: ' . print_r($blocks, true));
        $curr_schemas = [];

        if (!empty($videoUrls['youtube']) || !empty($videoUrls['dailymotion']) || !empty($videoUrls['vimeo'])) {
            foreach ($videoUrls as $type => $urls) {
                if (!empty($urls)) {
                    foreach ($urls as $url) {
                        $api_res = [];

                        switch ($type) {
                            case 'youtube':
                                $youtube_api_key = get_option('2good_vs_api_key');
                                if (empty($youtube_api_key)) {
                                    return;
                                }
                                $api_res = get_youtube_video_details($url, $youtube_api_key);
                                break;
                            case 'dailymotion':
                                $api_res = get_dailymotion_video_details($url);
                                break;
                            case 'vimeo':
                                $apikey = 'YOUR_VIMEO_API_KEY';
                                $api_res = get_vimeo_video_details($url, $apikey);
                                break;
                            default:
                                // Default case handler (if needed)
                                break;
                        }

                        if (!empty($api_res)) {
                            $the_schema = generate_video_schema($api_res, $post_id);
                            $curr_schemas[] = $the_schema;
                        }
                    }
                }   
            }

            //error_log('All schemas: ' . print_r($curr_schemas, true));
        
            if (!empty($curr_schemas)) {
                save_seo_video_schemas($post_id, $curr_schemas);
            } else {
                // If there are no video URLs, delete any existing schemas
                delete_post_meta($post_id, '2good_video_schemas');
            }

        } else {
            delete_post_meta($post_id, '2good_video_schemas');
            return;
        }

    }
}

add_action('elementor/editor/after_save', 'action_save_post_Elementor', 10, 2); // hook to Elementor 
add_action('save_post', 'action_save_post_Editors', 10, 2);  // hook to diff editors -> Gutenberg in our case