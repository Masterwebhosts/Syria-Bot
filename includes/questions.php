<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تسجيل الأسئلة التي لم يجد لها البوت إجابة.
 *
 * @param string $question السؤال الوارد.
 *
 * @return bool
 */
function syria_bot_log_question( $question ) {

	global $wpdb;

	if ( empty( $question ) ) {
		return false;
	}

	/*
	 * تنظيف السؤال.
	 */
	$question = sanitize_text_field(
		$question
	);

	if ( empty( $question ) ) {
		return false;
	}

	$table = $wpdb->prefix . 'ai_bot_questions';

	/*
	 * التأكد من وجود الجدول.
	 */
	$table_exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			'SHOW TABLES LIKE %s',
			$table
		)
	);

	if ( $table_exists !== $table ) {
		return false;
	}

	/*
	 * إنشاء نسخة موحدة من السؤال.
	 */
	$normalized = $question;

	if ( function_exists( 'syria_bot_normalize_text' ) ) {

		$normalized = syria_bot_normalize_text(
			$question
		);
	}


	/*
	 * البحث عن السؤال نفسه باستخدام النسخة الموحدة.
	 *
	 * اسم الجدول يتم توليده داخلياً من بادئة WordPress
	 * ولا يأتي من إدخال المستخدم.
	 */
	$existing = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			'SELECT id, question, count, status
			FROM ' . $wpdb->prefix . 'ai_bot_questions
			WHERE normalized_question = %s
			LIMIT 1',
			$normalized
		)
	); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared


	/*
	 * تحديث سؤال موجود.
	 */
	if ( $existing ) {

		$updated = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$table,
			array(
				'count' => absint(
					$existing->count
				) + 1,

				'updated_at' => current_time(
					'mysql'
				),
			),
			array(
				'id' => absint(
					$existing->id
				),
			),
			array(
				'%d',
				'%s',
			),
			array(
				'%d',
			)
		);

		return $updated !== false;
	}

	/*
	 * إضافة سؤال جديد.
	 */
	$inserted = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$table,
		array(
			'question' => $question,

			'normalized_question' => $normalized,

			'count' => 1,

			'ip' => isset( $_SERVER['REMOTE_ADDR'] )
				? sanitize_text_field(
					wp_unslash(
						$_SERVER['REMOTE_ADDR']
					)
				)
				: '',

			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] )
				? sanitize_text_field(
					wp_unslash(
						$_SERVER['HTTP_USER_AGENT']
					)
				)
				: '',

			'status' => 'new',

			'linked_post_id' => 0,

			'updated_at' => current_time(
				'mysql'
			),
		),
		array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
		)
	);

	/*
	 * Hook للمطورين مستقبلاً.
	 */
	if ( $inserted ) {

		do_action(
			'syria_bot_question_logged',
			$question
		);
	}

	return $inserted !== false;
}

