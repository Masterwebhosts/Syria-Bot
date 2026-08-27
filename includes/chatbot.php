<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * AJAX Actions
 */

add_action(
    'wp_ajax_syria_bot_chat',
    'syria_bot_chat'
);

add_action(
    'wp_ajax_nopriv_syria_bot_chat',
    'syria_bot_chat'
);



/**
 * Rate limit protection
 */

function syria_bot_check_rate_limit(){


    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';


    $key = 'syria_bot_limit_' . md5($ip);



    $count = get_transient($key);



    if($count && $count >= 20){

        return false;

    }



    set_transient(

        $key,

        intval($count)+1,

        60

    );


    return true;

}





/**
 * ØªØ´ØºÙŠÙ„ Ø§Ù„Ø¨ÙˆØª
 */

function syria_bot_chat(){



    if(
        !syria_bot_check_rate_limit()
    ){


        wp_send_json_error(

            array(

                'message'=>__(
                    'Too many requests. Please wait.',
                    'syria-bot'
                )

            )

        );

    }





    if(ob_get_length()){

        ob_clean();

    }





    if(

        !isset($_POST['nonce'])

        ||

        !wp_verify_nonce(

            sanitize_text_field(

                wp_unslash(
                    $_POST['nonce']
                )

            ),

            'syria_bot_nonce'

        )

    ){


        wp_send_json_error(

            array(

                'message'=>__(
                    'Invalid request.',
                    'syria-bot'
                )

            )

        );

    }






    $question = isset($_POST['question'])

        ?

        sanitize_text_field(

            wp_unslash(
                $_POST['question']
            )

        )

        :

        '';






    if(empty($question)){


        wp_send_json_error(

            array(

                'message'=>__(
                    'Please enter your question.',
                    'syria-bot'
                )

            )

        );

    }






    $answer = false;



    if(
        function_exists(
            'syria_bot_search'
        )
    ){


        $answer =
            syria_bot_search(
                $question
            );


    }





    if($answer){



        $final_answer =
            syria_bot_extract_answer(

                $answer->content,

                $question

            );





        wp_send_json_success(

            array(

                'answer'=>wp_kses_post(
                    $final_answer
                ),


                'title'=>sanitize_text_field(
                    $answer->title
                ),


                'url'=>esc_url(
                    $answer->url
                ),


                'category'=>sanitize_text_field(
                    $answer->category_name ?? ''
                ),


                'parent_category'=>sanitize_text_field(
                    $answer->parent_category ?? ''
                )


            )

        );


    }





    if(
        function_exists(
            'syria_bot_log_question'
        )
    ){


        syria_bot_log_question(
            $question
        );


    }






    wp_send_json_success(

        array(

            'answer'=>__(
                'No suitable answer found. Your question has been saved for improvement.',
                'syria-bot'
            )

        )

    );


}







/**
 * Ø§Ø³ØªØ®Ø±Ø§Ø¬ Ø£ÙØ¶Ù„ Ø¥Ø¬Ø§Ø¨Ø©
 */

function syria_bot_extract_answer(

    $content,

    $question

){



    $content =
        wp_strip_all_tags(
            $content
        );



    $content =
        preg_replace(

            '/\s+/u',

            ' ',

            $content

        );





    $sentences =
        preg_split(

            '/(?<=[.ØŸ!])\s+/u',

            $content

        );






    $keywords =
        explode(

            ' ',

            syria_bot_normalize_text(
                $question
            )

        );





    $matches=array();






    foreach($sentences as $sentence){



        $score=0;



        foreach($keywords as $word){



            if(

                mb_strlen(
                    $word,
                    'UTF-8'
                ) > 2


                &&


                mb_stripos(

                    $sentence,

                    $word

                ) !== false

            ){


                $score++;


            }


        }





        if($score>0){


            $matches[] = array(

                'score'=>$score,

                'text'=>$sentence

            );


        }


    }






    usort(

        $matches,

        function($a,$b){

            return $b['score'] <=> $a['score'];

        }

    );






    if(!empty($matches)){



        $limit =
            get_option(

                'syria_bot_answer_words',

                80

            );



        return wp_trim_words(

            $matches[0]['text'],

            $limit

        );


    }





    return wp_trim_words(

        $content,

        80

    );


}

