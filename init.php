<?php

function add_2good_video_schema() {
    if (is_single() || is_page()) {
        $post_id = get_the_ID();
        
        $video_schemas = get_post_meta($post_id, '2good_video_schemas', true);
        $all_schemas = [];

        if (!empty($video_schemas)) {
            $video_schemas = maybe_unserialize($video_schemas);
            
            foreach ($video_schemas as $schema) {
                // Add each schema to the all_schemas array
                $all_schemas[] = json_decode($schema, true);
            }

            if (!empty($all_schemas)) {
                // Encode all schemas into JSON and output in a single script tag
                echo '<script type="application/ld+json" class="2good-video-schemas">' . 
                     json_encode($all_schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . 
                     '</script>';
            }
        }
    }
}

add_action('wp_head', 'add_2good_video_schema', 1);

