<?php
/**
 * Uninstall Syria Bot.
 *
 * This file runs only when the plugin is deleted from WordPress.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

/*
 * Remove all plugin-owned database tables.
 * Never remove WordPress posts, pages, categories, or tags.
 */
$tables = array(
	$wpdb->prefix . 'ai_bot_knowledge',
	$wpdb->prefix . 'ai_bot_questions',
	$wpdb->prefix . 'ai_bot_categories',
);

foreach ( $tables as $table ) {
	$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"DROP TABLE IF EXISTS {$table}"
	);
}

/*
 * Remove plugin options.
 */
$options = array(
	'syria_bot_name',
	'syria_bot_welcome',
	'syria_bot_needs_database',
	'syria_bot_db_version',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

/*
 * Remove rate-limit transients created by the chatbot.
 * The IP/hash suffix is dynamic, so clean only our own transient prefix.
 */
$transient_prefix = $wpdb->esc_like( '_transient_syria_bot_limit_' ) . '%';
$timeout_prefix   = $wpdb->esc_like( '_transient_timeout_syria_bot_limit_' ) . '%';

$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options}
		WHERE option_name LIKE %s
		OR option_name LIKE %s",
		$transient_prefix,
		$timeout_prefix
	)
);
