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
        isset( $_POST['syria_bot_save_settings'] )
        &&
        isset( $_POST['syria_bot_settings_nonce'] )
        &&
        wp_verify_nonce(
            sanitize_text_field(
                wp_unslash( $_POST['syria_bot_settings_nonce'] )
            ),
            'syria_bot_save_settings'
        )
    ) {

        $bot_name = isset( $_POST['syria_bot_name'] )
            ? sanitize_text_field(
                wp_unslash( $_POST['syria_bot_name'] )
            )
            : '';

        $welcome = isset( $_POST['syria_bot_welcome'] )
            ? sanitize_textarea_field(
                wp_unslash( $_POST['syria_bot_welcome'] )
            )
            : '';

        update_option(
            'syria_bot_name',
            $bot_name
        );

        update_option(
            'syria_bot_welcome',
            $welcome
        );

        echo '<div class="notice notice-success is-dismissible"><p>';

        esc_html_e(
            'Settings saved successfully.',
            'syria-bot'
        );

        echo '</p></div>';
    }

    $bot_name = get_option(
        'syria_bot_name',
        'Trade Sphare Bot-ai'
    );

    $welcome = get_option(
        'syria_bot_welcome',
        'Hello! How can I help you?'
    );

    ?>

    <div class="wrap">

        <h1>
            <?php esc_html_e( 'Syria Bot Settings', 'syria-bot' ); ?>
        </h1>

        <form method="post">

            <?php
            wp_nonce_field(
                'syria_bot_save_settings',
                'syria_bot_settings_nonce'
            );
            ?>

            <table class="form-table">

                <tr>

                    <th scope="row">

                        <label for="syria_bot_name">
                            <?php
                            esc_html_e(
                                'Bot Name',
                                'syria-bot'
                            );
                            ?>
                        </label>

                    </th>

                    <td>

                        <input
                            type="text"
                            id="syria_bot_name"
                            name="syria_bot_name"
                            value="<?php echo esc_attr( $bot_name ); ?>"
                            class="regular-text"
                        >

                    </td>

                </tr>

                <tr>

                    <th scope="row">

                        <label for="syria_bot_welcome">
                            <?php
                            esc_html_e(
                                'Welcome Message',
                                'syria-bot'
                            );
                            ?>
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

            </table>

            <p class="submit">

                <button
                    type="submit"
                    name="syria_bot_save_settings"
                    class="button button-primary"
                >
                    <?php
                    esc_html_e(
                        'Save Settings',
                        'syria-bot'
                    );
                    ?>
                </button>

            </p>

        </form>

    </div>

    <?php
}

