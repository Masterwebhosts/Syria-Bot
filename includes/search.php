<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * تنظيف النص وتوحيده
 */
function syria_bot_normalize_text( $text ) {


    $text = wp_strip_all_tags($text);


    $text = mb_strtolower(
        $text,
        'UTF-8'
    );



    $text = str_replace(

        array(
            'أ',
            'إ',
            'آ',
            'ى'
        ),

        array(
            'ا',
            'ا',
            'ا',
            'ي'
        ),

        $text

    );



    $text = preg_replace(

        '/[\x{064B}-\x{065F}]/u',

        '',

        $text

    );



    $text = preg_replace(

        '/[^\p{L}\p{N}\s]/u',

        ' ',

        $text

    );



    $text = preg_replace(

        '/\s+/u',

        ' ',

        $text

    );



    return trim($text);

}






/**
 * كلمات يتم تجاهلها
 */
function syria_bot_stop_words() {


    return array(

        'كيف',
        'هل',
        'ما',
        'ماذا',
        'هو',
        'هي',
        'من',
        'في',
        'على',
        'عن',
        'الى',
        'إلى',
        'اريد',
        'يمكن',
        'please',
        'how',
        'what',
        'the',
        'is'

    );

}






/**
 * توسيع الكلمات
 */
function syria_bot_expand_words($words){


    $synonyms = array(


        'حساب'=>array(
            'عضوية',
            'account',
            'profile'
        ),


        'تسجيل'=>array(
            'انشاء',
            'فتح',
            'register',
            'signup'
        ),


        'دخول'=>array(
            'login',
            'signin'
        ),


        'شراء'=>array(
            'طلب',
            'order',
            'buy'
        )


    );



    $expanded=$words;



    foreach($words as $word){


        if(isset($synonyms[$word])){


            foreach($synonyms[$word] as $item){


                $expanded[] =
                    syria_bot_normalize_text($item);


            }


        }


    }



    return array_unique($expanded);


}







/**
 * محرك البحث الذكي
 */
function syria_bot_search($question){


    global $wpdb;



    $table =
        $wpdb->prefix . 'ai_bot_knowledge';





    $question =
        syria_bot_normalize_text(
            $question
        );




    if(empty($question)){

        return false;

    }






    $words =
        explode(
            ' ',
            $question
        );




    $keywords=array();




    foreach($words as $word){



        if(

            mb_strlen(
                $word,
                'UTF-8'
            ) > 2


            &&


            !in_array(
                $word,
                syria_bot_stop_words(),
                true
            )


        ){


            $keywords[]=$word;


        }


    }






    $keywords =
        syria_bot_expand_words(
            $keywords
        );

      $results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	'SELECT
		id,
		title,
		content,
		keywords,
		tags,
		category_name,
		parent_category,
		url
	FROM ' . $wpdb->prefix . 'ai_bot_knowledge'
); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared

    if(empty($results)){

        return false;

    }






    $best=null;

    $highest_score=0;







    foreach($results as $row){



        $score=0;




        $fields=array(

            'title'=>15,

            'keywords'=>10,

            'tags'=>10,

            'category_name'=>12,

            'parent_category'=>8,

            'content'=>3

        );






        foreach($keywords as $keyword){



            foreach($fields as $field=>$points){



                $value =
                    syria_bot_normalize_text(
                        $row->$field
                    );



                if(
                    mb_strpos(
                        $value,
                        $keyword
                    ) !== false
                ){


                    $score += $points;


                }


            }


        }





        if(
            mb_strpos(
                syria_bot_normalize_text($row->title),
                $question
            ) !== false
        ){


            $score += 25;


        }






        if($score > $highest_score){


            $highest_score=$score;

            $best=$row;


        }


    }







    $min_score =
        get_option(
            'syria_bot_min_score',
            8
        );





    if(

        $best

        &&

        $highest_score >= $min_score

    ){


        return $best;


    }

    return false;

}