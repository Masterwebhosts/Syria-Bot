
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Syria Bot settings page.
 */
function syria_bot_settings_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if (
        isset( $_POST['syria_bot_save_settings'] ) &&
        check_admin_referer(
            'syria_bot_settings_save',
            'syria_bot_settings_nonce'
        )
    ) {

        update_option(
            'syria_bot_name',
            sanitize_text_field(
                wp_unslash(
                    isset( $_POST['syria_bot_name'] )
                        ? $_POST['syria_bot_name']
                        : ''
                )
            )
        );

        update_option(
            'syria_bot_welcome',
            sanitize_textarea_field(
                wp_unslash(
                    isset( $_POST['syria_bot_welcome'] )
                        ? $_POST['syria_bot_welcome']
                        : ''
                )
            )
        );

        update_option(
            'syria_bot_answer_words',
            absint(
                wp_unslash(
                    isset( $_POST['syria_bot_answer_words'] )
                        ? $_POST['syria_bot_answer_words']
                        : 0
                )
            )
        );

        update_option(
            'syria_bot_min_score',
            absint(
                wp_unslash(
                    isset( $_POST['syria_bot_min_score'] )
                        ? $_POST['syria_bot_min_score']
                        : 0
                )
            )
        );

        echo '<div class="notice notice-success"><p>';
        echo esc_html__(
            'Settings saved successfully.',
            'syria-bot'
        );
        echo '</p></div>';
    }

    $name = get_option(
        'syria_bot_name',
        'Trade Sphare Bot-ai'
    );

    $welcome = get_option(
        'syria_bot_welcome',
        'Hello! How can I help you?'
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
            <?php esc_html_e( 'Syria Bot Settings', 'syria-bot' ); ?>
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
                        <label for="syria_bot_name">
                            <?php esc_html_e(
                                'Bot Name',
                                'syria-bot'
                            ); ?>
                        </label>
                    </th>

                    <td>
                        <input
                            type="text"
                            id="syria_bot_name"
                            name="syria_bot_name"
                            value="<?php echo esc_attr( $name ); ?>"
                            class="regular-text"
                        >
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="syria_bot_welcome">
                            <?php esc_html_e(
                                'Welcome Message',
                                'syria-bot'
                            ); ?>
                        </label>
                    </th>

                    <td>
                        <textarea
                            id="syria_bot_welcome"
                            name="syria_bot_welcome"
                            rows="5"
                            class="large-text"
                        ><?php echo esc_textarea( $welcome ); ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="syria_bot_answer_words">
                            <?php esc_html_e(
                                'Answer Word Count',
                                'syria-bot'
                            ); ?>
                        </label>
                    </th>

                    <td>
                        <input
                            type="number"
                            id="syria_bot_answer_words"
                            name="syria_bot_answer_words"
                            value="<?php echo esc_attr( $words ); ?>"
                            min="1"
                        >
                    </td>
                </tr>

                <tr>
                    <th>
                        <label for="syria_bot_min_score">
                            <?php esc_html_e(
                                'Minimum Match Score',
                                'syria-bot'
                            ); ?>
                        </label>
                    </th>

                    <td>
                        <input
                            type="number"
                            id="syria_bot_min_score"
                            name="syria_bot_min_score"
                            value="<?php echo esc_attr( $score ); ?>"
                            min="0"
                        >
                    </td>
                </tr>

            </table>

            <button
                type="submit"
                name="syria_bot_save_settings"
                class="button button-primary"
            >
                <?php esc_html_e(
                    'Save Settings',
                    'syria-bot'
                ); ?>
            </button>

        </form>

    </div>

    <?php
}
