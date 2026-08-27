<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * تصنيف المقالات الجديدة تلقائياً
 */

add_action(
    'publish_post',
    'syria_bot_auto_category'
);



function syria_bot_auto_category( $post_id ) {


    // منع التكرار أثناء الحفظ
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }



    $post = get_post( $post_id );


    if ( ! $post ) {
        return;
    }



    $content = strtolower(
        $post->post_title . ' ' . $post->post_content
    );



    /*
    |--------------------------------------------------------------------------
    | جلب التصنيفات الموجودة
    |--------------------------------------------------------------------------
    */


    $categories = get_categories(
        array(
            'hide_empty' => false
        )
    );



    if ( empty($categories) ) {
        return;
    }



    $best_category = 0;
    $best_score = 0;



    foreach ( $categories as $category ) {


        $score = 0;


        $keywords = array(

            $category->name,

            str_replace(
                '-',
                ' ',
                $category->slug
            )

        );



        foreach ( $keywords as $keyword ) {


            $keyword = strtolower(
                trim($keyword)
            );


            if (
                ! empty($keyword)
                &&
                strpos(
                    $content,
                    $keyword
                ) !== false
            ) {

                $score++;

            }


        }



        if ( $score > $best_score ) {


            $best_score = $score;


            $best_category = $category->term_id;


        }


    }



    /*
    |--------------------------------------------------------------------------
    | إضافة التصنيف المناسب
    |--------------------------------------------------------------------------
    */


    if (
        $best_category
        &&
        $best_score > 0
    ) {


        wp_set_post_categories(
            $post_id,
            array(
                $best_category
            ),
            false
        );


    }


}