<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * صفحة الأسئلة الواردة.
 */
function syria_bot_questions_page() {


    if ( ! current_user_can( 'manage_options' ) ) {

        return;

    }


    global $wpdb;


    $table = $wpdb->prefix . 'ai_bot_questions';



    /**
     * التأكد من وجود الجدول
     */
    $exists = $wpdb->get_var(

        $wpdb->prepare(

            "SHOW TABLES LIKE %s",

            $table

        )

    );


    if ( $exists !== $table ) {


        echo '<div class="notice notice-error"><p>';

        esc_html_e(
            'Questions table does not exist.',
            'syria-bot'
        );

        echo '</p></div>';


        return;

    }




    /**
     * حذف سؤال
     */
    if (

        isset( $_GET['delete_question'] )

        &&

        isset( $_GET['_wpnonce'] )

        &&

        wp_verify_nonce(

            sanitize_text_field(
                wp_unslash(
                    $_GET['_wpnonce']
                )
            ),

            'syria_bot_delete_question'

        )

    ) {


        $id = absint(
            $_GET['delete_question']
        );


        if ( $id ) {


            $wpdb->delete(

                $table,

                array(
                    'id' => $id
                ),

                array(
                    '%d'
                )

            );


            echo '<div class="notice notice-success"><p>';

            esc_html_e(
                'Question deleted successfully.',
                'syria-bot'
            );

            echo '</p></div>';


        }

    }





    /**
     * تحديث حالة السؤال
     */
    if (

        isset( $_GET['change_status'] )

        &&

        isset( $_GET['status'] )

        &&

        isset( $_GET['_wpnonce'] )

        &&

        wp_verify_nonce(

            sanitize_text_field(
                wp_unslash(
                    $_GET['_wpnonce']
                )
            ),

            'syria_bot_change_status'

        )

    ) {


        $id = absint(
            $_GET['change_status']
        );


        $status = sanitize_text_field(

            wp_unslash(
                $_GET['status']
            )

        );


        $allowed = array(

            'new',

            'reviewed',

            'answered'

        );


        if (

            $id

            &&

            in_array(
                $status,
                $allowed,
                true
            )

        ) {


            $wpdb->update(

                $table,

                array(

                    'status' => $status,

                    'updated_at' => current_time(
                        'mysql'
                    )

                ),

                array(

                    'id' => $id

                ),

                array(

                    '%s',

                    '%s'

                ),

                array(

                    '%d'

                )

            );



            echo '<div class="notice notice-success"><p>';

            esc_html_e(
                'Status updated successfully.',
                'syria-bot'
            );

            echo '</p></div>';


        }


    }







    /**
     * البحث
     */
    $search = isset(
        $_GET['syria_question_search']
    )

        ? sanitize_text_field(

            wp_unslash(
                $_GET['syria_question_search']
            )

        )

        : '';




    $where = '';

    $params = array();



    if ( ! empty( $search ) ) {


        $where = "WHERE question LIKE %s";


        $params[] = '%' .
            $wpdb->esc_like(
                $search
            )
            . '%';


    }




    $sql = "

        SELECT

            id,
            question,
            count,
            status,
            created_at,
            updated_at

        FROM {$table}

        {$where}

        ORDER BY count DESC, created_at DESC

        LIMIT 100

    ";




    if ( ! empty( $params ) ) {


        $questions = $wpdb->get_results(

            $wpdb->prepare(

                $sql,

                $params

            )

        );


    } else {


        $questions = $wpdb->get_results(
            $sql
        );


    }




   ?> 



    <div class="wrap">


        <h1>

            <?php esc_html_e(
                'Incoming Questions',
                'syria-bot'
            ); ?>

        </h1>




        <form method="get">


            <input
                type="hidden"
                name="page"
                value="syria-bot-questions"
            >



            <input

                type="search"

                name="syria_question_search"

                value="<?php echo esc_attr( $search ); ?>"

                placeholder="<?php esc_attr_e(
                    'Search questions',
                    'syria-bot'
                ); ?>"

            >



            <button class="button">

                <?php esc_html_e(
                    'Search',
                    'syria-bot'
                ); ?>

            </button>


        </form>




        <br>




        <table class="widefat striped">


            <thead>

                <tr>


                    <th>
                        #
                    </th>


                    <th>
                        <?php esc_html_e(
                            'Question',
                            'syria-bot'
                        ); ?>
                    </th>


                    <th>
                        <?php esc_html_e(
                            'Count',
                            'syria-bot'
                        ); ?>
                    </th>


                    <th>
                        <?php esc_html_e(
                            'Status',
                            'syria-bot'
                        ); ?>
                    </th>


                    <th>
                        <?php esc_html_e(
                            'Updated',
                            'syria-bot'
                        ); ?>
                    </th>


                    <th>
                        <?php esc_html_e(
                            'Actions',
                            'syria-bot'
                        ); ?>
                    </th>


                </tr>


            </thead>



            <tbody>


            <?php if ( ! empty( $questions ) ) : ?>


                <?php foreach ( $questions as $question ) : ?>


                    <tr>


                        <td>
                            <?php echo esc_html(
                                $question->id
                            ); ?>
                        </td>



                        <td>
                            <?php echo esc_html(
                                $question->question
                            ); ?>
                        </td>



                        <td>
                            <?php echo esc_html(
                                $question->count
                            ); ?>
                        </td>



                        <td>
                            <?php echo esc_html(
                                $question->status
                            ); ?>
                        </td>



                        <td>
                            <?php echo esc_html(
                                $question->updated_at
                            ); ?>
                        </td>




                        <td>



                        <?php

                        $review = wp_nonce_url(

                            admin_url(
                                'admin.php?page=syria-bot-questions&change_status=' .
                                $question->id .
                                '&status=reviewed'
                            ),

                            'syria_bot_change_status'

                        );



                        $answered = wp_nonce_url(

                            admin_url(
                                'admin.php?page=syria-bot-questions&change_status=' .
                                $question->id .
                                '&status=answered'
                            ),

                            'syria_bot_change_status'

                        );



                        $delete = wp_nonce_url(

                            admin_url(
                                'admin.php?page=syria-bot-questions&delete_question=' .
                                $question->id
                            ),

                            'syria_bot_delete_question'

                        );

                        ?>



                        <a href="<?php echo esc_url( $review ); ?>">
                            <?php esc_html_e(
                                'Review',
                                'syria-bot'
                            ); ?>
                        </a>


                        |


                        <a href="<?php echo esc_url( $answered ); ?>">
                            <?php esc_html_e(
                                'Answered',
                                'syria-bot'
                            ); ?>
                        </a>


                        |


                        <a

                        style="color:red"

                        onclick="return confirm('<?php esc_attr_e(
                            'Delete question?',
                            'syria-bot'
                        ); ?>')"

                        href="<?php echo esc_url( $delete ); ?>">

                            <?php esc_html_e(
                                'Delete',
                                'syria-bot'
                            ); ?>

                        </a>



                        </td>



                    </tr>



                <?php endforeach; ?>


            <?php else : ?>


                <tr>

                    <td colspan="6">

                        <?php esc_html_e(
                            'No questions found.',
                            'syria-bot'
                        ); ?>

                    </td>

                </tr>


        <?php endif; ?>


            </tbody>


        </table>

    </div>
  <?php

} 