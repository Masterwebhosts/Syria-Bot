<?php

if ( ! defined('ABSPATH') ) {
    exit;
}


/**
 * صفحة إعدادات Syria Bot
 */


function syria_bot_settings_page(){


    if ( ! current_user_can('manage_options') ) {

        return;

    }



    if (

        isset($_POST['syria_bot_save_settings'])

        &&

        check_admin_referer(
            'syria_bot_settings_save',
            'syria_bot_settings_nonce'
        )

    ) {


        update_option(

            'syria_bot_name',

            sanitize_text_field(
                $_POST['syria_bot_name']
            )

        );



        update_option(

            'syria_bot_welcome',

            sanitize_textarea_field(
                $_POST['syria_bot_welcome']
            )

        );



        update_option(

            'syria_bot_answer_words',

            absint(
                $_POST['syria_bot_answer_words']
            )

        );



        update_option(

            'syria_bot_min_score',

            absint(
                $_POST['syria_bot_min_score']
            )

        );



        echo '<div class="notice notice-success"><p>';

        echo 'تم حفظ الإعدادات';

        echo '</p></div>';


    }



    $name = get_option(
        'syria_bot_name',
        'Syria Bot'
    );


    $welcome = get_option(

        'syria_bot_welcome',

        'أهلاً بك، كيف يمكنني مساعدتك؟'

    );


    $words = get_option(

        'syria_bot_answer_words',

        80

    );


    $score = get_option(

        'syria_bot_min_score',

        8

    );


    ?>


    <div class="wrap">


        <h1>
            إعدادات Syria Bot
        </h1>



        <form method="post">


            <?php

            wp_nonce_field(

                'syria_bot_settings_save',

                'syria_bot_settings_nonce'

            );

            ?>



            <table class="form-table">


                <tr>

                    <th>
                        اسم البوت
                    </th>


                    <td>

                        <input

                        type="text"

                        name="syria_bot_name"

                        value="<?php echo esc_attr($name); ?>"

                        class="regular-text">

                    </td>


                </tr>



                <tr>

                    <th>
                        رسالة الترحيب
                    </th>


                    <td>

                        <textarea

                        name="syria_bot_welcome"

                        rows="5"

                        class="large-text"><?php echo esc_textarea($welcome); ?></textarea>

                    </td>


                </tr>



                <tr>

                    <th>
                        عدد كلمات الإجابة
                    </th>


                    <td>

                        <input

                        type="number"

                        name="syria_bot_answer_words"

                        value="<?php echo esc_attr($words); ?>">

                    </td>


                </tr>



                <tr>

                    <th>
                        أقل درجة تطابق
                    </th>


                    <td>

                        <input

                        type="number"

                        name="syria_bot_min_score"

                        value="<?php echo esc_attr($score); ?>">

                    </td>


                </tr>



            </table>



            <button

            type="submit"

            name="syria_bot_save_settings"

            class="button button-primary">

                حفظ الإعدادات

            </button>



        </form>


    </div>


    <?php

}
