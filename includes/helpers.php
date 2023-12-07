<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
};

function find_video_widget_Elementor($elements) {
    $videoUrls = [
        'youtube' => [],
        'vimeo' => [],
        'dailymotion' => [],
        'videopress' => []
    ];

    foreach ($elements as $element) {
        if (isset($element['widgetType']) && $element['widgetType'] === 'video') {
            $settings = $element['settings'];

            // Handle YouTube URLs (assuming no 'video_type' is set for YouTube -> logic from the logged data)
            if (isset($settings['youtube_url']) && empty($settings['video_type'])) {
                $videoUrls['youtube'][] = $settings['youtube_url'];
            }

            // Handle other video types (rest has vide_type prop -> logic from the logged data)
            if (isset($settings['video_type'])) {
                $type = $settings['video_type'];
                if (isset($settings[$type . '_url'])) {
                    $videoUrls[$type][] = $settings[$type . '_url'];
                }
            }
        }

        // Recursively search in nested elements
        if (!empty($element['elements']) && is_array($element['elements'])) {
            $nestedUrls = find_video_widget_Elementor($element['elements']);
            foreach ($videoUrls as $type => &$urls) {
                $urls = array_merge($urls, $nestedUrls[$type]);
            }
        }
    }
    $videoUrls = extract_video_ids_from_urls($videoUrls);

    return $videoUrls;
}

function extract_video_ids_from_urls($videoUrls) {
    foreach ($videoUrls as $type => &$urls) {
        foreach ($urls as $key => $url) {
            switch ($type) {
                case 'youtube':
                    if (preg_match('/youtube\.com\/watch\?v=([^\s\"\'<>&]+)/', $url, $matches)) {
                        $urls[$key] = $matches[1];
                    }
                    break;
                case 'vimeo':
                    if (preg_match('/vimeo\.com\/([^\s\"\'<>&]+)/', $url, $matches)) {
                        $urls[$key] = $matches[1];
                    }
                    break;
                case 'dailymotion':
                    if (preg_match('/dailymotion\.com\/video\/([^\s\"\'<>&?]+)/', $url, $matches)) {
                        $urls[$key] = $matches[1];
                    }
                    break;
            }
        }
        $urls = array_unique($urls); // Remove duplicates
    }
    return $videoUrls;
}

function find_video_block_Gutenberg($blocks) {
    $videoUrls = [
        'youtube' => [],
        'vimeo' => [],
        'dailymotion' => []
    ];

    foreach ($blocks as $block) {
        if (isset($block['attrs']['url'])) {
            $url = $block['attrs']['url'];

            if (preg_match('/youtube\.com\/watch\?v=([^\s\"\'<>&]+)/', $url, $matches) ||
                preg_match('/youtu\.be\/([^\s\"\'<>&]+)/', $url, $matches)) {
                $videoUrls['youtube'][] = $matches[1];
            } elseif (preg_match('/vimeo\.com\/([^\s\"\'<>&]+)/', $url, $matches)) {
                $videoUrls['vimeo'][] = $matches[1];
            } elseif (preg_match('/dailymotion\.com\/video\/([^\s\"\'<>&?]+)/', $url, $matches)) {
                $videoUrls['dailymotion'][] = $matches[1];
            }
        }

        if (!empty($block['innerBlocks'])) {
            $nestedUrls = find_video_block_Gutenberg($block['innerBlocks']);
            foreach ($videoUrls as $type => &$urls) {
                $urls = array_merge($urls, $nestedUrls[$type]);
            }
        }
    }

    // Remove duplicates for each type
    foreach ($videoUrls as $type => &$urls) {
        $urls = array_unique($urls);
    }

    return $videoUrls;
}

function generate_video_schema($api_res, $post_id) {

    $video_data = [
        'name' => $api_res['snippet']['title'],
        'thumbnail_url' => $api_res['snippet']['thumbnails']['standard']['url'],
        'url' => 'https://www.youtube.com/watch?v=' . $api_res['id'],
        'uploadDate' => $api_res['snippet']['publishedAt']
    ];

    // Normalize newlines and handle quotes
    $description = str_replace(["\r", "\n"], ' ', $api_res['snippet']['description']);
    $description = str_replace(['”', '“'], '"', $description);

    // Sanitize the description
    $description = wp_kses_post($description);
    // Create the schema array

    $schema = [
        "@context" => "https://schema.org/",
        "@type" => "VideoObject",
        //"@id" => get_permalink($post_id) . '#videoobject',
        "url" => get_permalink($post_id),
        "name" => $video_data['name'],
        "uploadDate" => $video_data['uploadDate'],
        "description" => $description,
        "thumbnailUrl" => $video_data['thumbnail_url'],
        "contentUrl" => $video_data['url'],
    ];
    // Add JSON_PRETTY_PRINT for debugging if needed
    // Encode to JSON with special characters properly escaped

    $json_schema = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // Check if json_encode succeeded
    if ($json_schema === false) {
        error_log('JSON encoding failed: ' . json_last_error_msg());
        return '';  // Return empty string or handle the error as appropriate
    }

    return  $json_schema;

}

function save_seo_video_schemas($post_id, $curr_schemas) {
    // Update the post meta with the new schemas
    update_post_meta($post_id, '2good_video_schemas', wp_slash($curr_schemas));
    //error_log('Video schemas saved/updated for post ' . $post_id);
}