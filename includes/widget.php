<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * واجهة البوت الأمامية.
 */
function syria_bot_widget() {

    $bot_name = get_option(
        'syria_bot_name',
        'Trade Sphare Bot-ai'
    );

    $welcome = get_option(
        'syria-bot_welcome',
        __(
            'مرحبا! كيف يمنكنني مساعدتك؟',
            'syria-bot'
        )
    );

    ?>

    <div id="syria-bot-wrapper">

        <div id="syria-bot-floating">

            <div id="syria-bot-box">

                <div class="syria-bot-header">
                    🤖
                    <?php echo esc_html( $bot_name ); ?>
                </div>

                <div id="syria-bot-welcome">
                    <?php
                    echo nl2br(
                        esc_html( $welcome )
                    );
                    ?>

                    <br><br>

                    🤖

                    <span>
                        <?php
                        echo esc_html__(
                            'يمكنني مساعدتك في العثور على معلومات من هذا الموقع الإلكتروني.',
                            'syria-bot'
                        );
                        ?>
                    </span>
                </div>

                <input id="syria-bot-question" type="text" autocomplete="off" placeholder="<?php echo esc_attr__( 'أكتب سؤالك هنا...', 'syria-bot' ); ?>">

                <button id="syria-bot-send" type="button"><?php echo esc_html__( 'إرسال', 'syria-bot' ); ?></button>

                <div id="syria-bot-answer"></div>

            </div>

            <button
                id="syria-bot-toggle"
                type="button"
                aria-label="<?php echo esc_attr( $bot_name ); ?>"
            >🤖</button>

        </div>

    </div>

    <?php
}

add_action(
    'wp_footer',
    'syria_bot_widget',
    999
);