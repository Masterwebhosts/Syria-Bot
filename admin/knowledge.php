<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * صفحة قاعدة المعرفة.
 */
function syria_bot_knowledge_page() {

    global $wpdb;

    $table = $wpdb->prefix . 'ai_bot_knowledge';

    $count = (int) $wpdb->get_var(
        $wpdb->prepare(
            'SELECT COUNT(*) FROM %i',
            $table
        )
    );

    $articles = $wpdb->get_results(
        $wpdb->prepare(
            'SELECT * FROM %i ORDER BY updated_at DESC',
            $table
        )
    );

    ?>

    <div class="wrap">

        <h1>
            <?php echo esc_html__(
                'Syria Bot',
                'syria-bot'
            ); ?>
        </h1>

        <h2>
            <?php echo esc_html__(
                'Knowledge Base',
                'syria-bot'
            ); ?>
        </h2>

        <p>
            <?php echo esc_html__(
                'Stored knowledge articles:',
                'syria-bot'
            ); ?>

            <strong>
                <?php echo esc_html( $count ); ?>
            </strong>
        </p>

        <button
            id="syria-bot-start-import"
            class="button button-primary"
        >
            <?php esc_html_e(
                'Start Knowledge Update',
                'syria-bot'
            ); ?>
        </button>

        <div
            id="syria-bot-progress"
            style="margin-top:20px;"
        ></div>

        <hr>

        <h2>
            <?php echo esc_html__(
                'Stored Articles',
                'syria-bot'
            ); ?>
        </h2>

        <table class="widefat fixed striped">

            <thead>

                <tr>

                    <th>
                        <?php esc_html_e(
                            'Title',
                            'syria-bot'
                        ); ?>
                    </th>

                    <th>
                        <?php esc_html_e(
                            'Main Category',
                            'syria-bot'
                        ); ?>
                    </th>

                    <th>
                        <?php esc_html_e(
                            'Sub Category',
                            'syria-bot'
                        ); ?>
                    </th>

                    <th>
                        <?php esc_html_e(
                            'Tags',
                            'syria-bot'
                        ); ?>
                    </th>

                    <th>
                        <?php esc_html_e(
                            'Updated',
                            'syria-bot'
                        ); ?>
                    </th>

                </tr>

            </thead>

            <tbody>

            <?php if ( ! empty( $articles ) ) : ?>

                <?php foreach ( $articles as $article ) : ?>

                    <tr>

                        <td>

                            <a
                                href="<?php echo esc_url( $article->url ); ?>"
                                target="_blank"
                            >
                                <?php echo esc_html( $article->title ); ?>
                            </a>

                        </td>

                        <td>
                            <?php echo esc_html( $article->category_name ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $article->parent_category ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $article->tags ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $article->updated_at ); ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr>

                    <td colspan="5">

                        <?php esc_html_e(
                            'No articles found.',
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