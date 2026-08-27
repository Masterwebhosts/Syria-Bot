<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Ø­ÙØ¸ Ø´Ø¬Ø±Ø© Ø§Ù„ØªØµÙ†ÙŠÙØ§Øª
 */
function syria_bot_save_category_tree( $term ) {


    global $wpdb;


    $table = $wpdb->prefix . 'ai_bot_categories';


    $parent_id = 0;



    // Ø­ÙØ¸ Ø§Ù„Ø£Ø¨ Ø£ÙˆÙ„Ø§Ù‹
    if ( $term->parent ) {


        $parent = get_term(
            $term->parent,
            'category'
        );


        if (
            $parent
            &&
            ! is_wp_error( $parent )
        ) {


            $parent_id = syria_bot_save_category_tree(
                $parent
            );


        }


    }



 $exists = $wpdb->get_var(
    $wpdb->prepare(
        'SELECT id FROM %i WHERE post_id = %d',
        $table,
        $post->ID
    )
);



    if ( $exists ) {


        return $exists;


    }



    // Ø¥Ø¶Ø§ÙØ© Ø§Ù„ØªØµÙ†ÙŠÙ
    $wpdb->insert(
        $table,
        array(

            'category_id' => absint(
                $term->term_id
            ),

            'parent_id' => absint(
                $parent_id
            ),

            'name' => sanitize_text_field(
                $term->name
            ),

            'slug' => sanitize_title(
                $term->slug
            ),

            'description' => sanitize_textarea_field(
                $term->description
            ),

            'created_at' => current_time(
                'mysql'
            ),

            'updated_at' => current_time(
                'mysql'
            ),

        )
    );


    return $wpdb->insert_id;


}





/**
 * AJAX Import
 */

add_action(
    'wp_ajax_syria_bot_import_batch',
    'syria_bot_import_batch'
);



function syria_bot_import_batch(){



    check_ajax_referer(
        'syria_bot_import',
        'nonce'
    );



    if (
        ! current_user_can(
            'manage_options'
        )
    ) {


        wp_send_json_error(
            array(
                'message' => 'Permission denied'
            )
        );


    }



    global $wpdb;



    $table = $wpdb->prefix . 'ai_bot_knowledge';



    $offset = absint(
        get_option(
            'syria_bot_import_offset',
            0
        )
    );



    $limit = 20;



    $posts = get_posts(
        array(

            'post_type' => array(
                'post',
                'page'
            ),

            'post_status' => 'publish',

            'numberposts' => $limit,

            'offset' => $offset,

            'orderby' => 'ID',

            'order' => 'ASC',

        )
    );




    if ( empty($posts) ) {


        delete_option(
            'syria_bot_import_offset'
        );


        wp_send_json_success(
            array(

                'finished' => true,

                'message' => 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ù‚Ø§Ø¹Ø¯Ø© Ø§Ù„Ù…Ø¹Ø±ÙØ© Ø¨Ù†Ø¬Ø§Ø­'

            )
        );


    }





    foreach ( $posts as $post ) {



        $content = apply_filters(
            'the_content',
            $post->post_content
        );


        $content = wp_strip_all_tags(
            $content
        );



        /*
        |--------------------------------------------------------------------------
        | Ø§Ù„ØªØµÙ†ÙŠÙØ§Øª
        |--------------------------------------------------------------------------
        */


        $category_id = 0;

        $category_name = '';

        $parent_category = '';




        $categories = wp_get_post_terms(
            $post->ID,
            'category'
        );




        if (
            ! empty($categories)
            &&
            ! is_wp_error($categories)
        ) {



            $category = $categories[0];



            // Ø¨Ù†Ø§Ø¡ Ø§Ù„Ø´Ø¬Ø±Ø©
            syria_bot_save_category_tree(
                $category
            );



            $category_id = absint(
                $category->term_id
            );



            $category_name = $category->name;




            if ( $category->parent ) {



                $parent = get_term(
                    $category->parent,
                    'category'
                );



                if (
                    $parent
                    &&
                    ! is_wp_error($parent)
                ) {


                    $parent_category = $parent->name;


                }



            } else {


                $parent_category = $category->name;


            }


        }




        /*
        |--------------------------------------------------------------------------
        | Ø§Ù„ÙˆØ³ÙˆÙ…
        |--------------------------------------------------------------------------
        */


        $tags = array();



        $post_tags = get_the_tags(
            $post->ID
        );



        if ( ! empty($post_tags) ) {


            foreach ( $post_tags as $tag ) {


                $tags[] = $tag->name;


            }


        }




        /*
        |--------------------------------------------------------------------------
        | Ø­ÙØ¸ Ø§Ù„Ù…Ø¹Ø±ÙØ©
        |--------------------------------------------------------------------------
        */


        $data = array(


            'post_id' => absint(
                $post->ID
            ),


            'title' => sanitize_text_field(
                $post->post_title
            ),


            'content' => $content,


            'keywords' => '',


            'url' => esc_url_raw(
                get_permalink(
                    $post->ID
                )
            ),


            'category_id' => $category_id,


            'category_name' => sanitize_text_field(
                $category_name
            ),


            'parent_category' => sanitize_text_field(
                $parent_category
            ),


            'tags' => sanitize_text_field(
                implode(
                    ', ',
                    $tags
                )
            ),


            'updated_at' => current_time(
                'mysql'
            ),

        );




     $exists = $wpdb->get_var(
    $wpdb->prepare(
        'SELECT id FROM %i WHERE post_id = %d',
        $table,
        $post->ID
    )
);



        if ( $exists ) {


            $wpdb->update(
                $table,
                $data,
                array(
                    'post_id' => $post->ID
                )
            );


        } else {


            $data['created_at'] = current_time(
                'mysql'
            );


            $wpdb->insert(
                $table,
                $data
            );


        }



    }




    update_option(
        'syria_bot_import_offset',
        $offset + $limit
    );




    wp_send_json_success(
        array(

            'finished' => false,

            'count' => count($posts),

            'offset' => $offset + $limit

        )
    );



}
