<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * صفحة الأسئلة الواردة.
 *
 * @return void
 */
function syria_bot_questions_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	global $wpdb;

	$table = $wpdb->prefix . 'ai_bot_questions';

	/*
	 * التحقق من وجود جدول الأسئلة.
	 */
	$exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			'SHOW TABLES LIKE %s',
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

	/*
	 * حذف سؤال.
	 */
	if (
		isset( $_GET['delete_question'], $_GET['_wpnonce'] ) &&
		wp_verify_nonce(
			sanitize_text_field(
				wp_unslash( $_GET['_wpnonce'] )
			),
			'syria_bot_delete_question'
		)
	) {

		$id = absint(
			$_GET['delete_question']
		);

		if ( $id ) {

			$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$table,
				array(
					'id' => $id,
				),
				array(
					'%d',
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

	/*
	 * تغيير حالة السؤال.
	 */
	if (
		isset(
			$_GET['change_status'],
			$_GET['status'],
			$_GET['_wpnonce']
		) &&
		wp_verify_nonce(
			sanitize_text_field(
				wp_unslash( $_GET['_wpnonce'] )
			),
			'syria_bot_change_status'
		)
	) {

		$id = absint(
			$_GET['change_status']
		);

		$status = sanitize_text_field(
			wp_unslash( $_GET['status'] )
		);

		$allowed_statuses = array(
			'new',
			'reviewed',
			'answered',
		);

		if (
			$id &&
			in_array(
				$status,
				$allowed_statuses,
				true
			)
		) {

			$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$table,
				array(
					'status'     => $status,
					'updated_at' => current_time( 'mysql' ),
				),
				array(
					'id' => $id,
				),
				array(
					'%s',
					'%s',
				),
				array(
					'%d',
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

	/*
	 * البحث.
	 */
	$search = isset( $_GET['syria_question_search'] )
		? sanitize_text_field(
			wp_unslash(
				$_GET['syria_question_search']
			)
		)
		: '';

	/*
	 * جلب الأسئلة.
	 *
	 * اسم الجدول يتم توليده داخلياً من بادئة WordPress.
	 */
	if ( '' !== $search ) {

		$questions = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT id, question, count, status, created_at, updated_at
				FROM ' . $wpdb->prefix . 'ai_bot_questions
				WHERE question LIKE %s
				ORDER BY count DESC, created_at DESC
				LIMIT %d',
				'%' . $wpdb->esc_like( $search ) . '%',
				100
			)
		);

	} else {

		$questions = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT id, question, count, status, created_at, updated_at
				FROM ' . $wpdb->prefix . 'ai_bot_questions
				ORDER BY count DESC, created_at DESC
				LIMIT %d',
				100
			)
		);
	}
	?>

	<div class="wrap">

		<h1>
			<?php
			esc_html_e(
				'Incoming Questions',
				'syria-bot'
			);
			?>
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
				placeholder="<?php esc_attr_e( 'Search questions', 'syria-bot' ); ?>"
			>

			<button
				class="button"
				type="submit"
			>
				<?php
				esc_html_e(
					'Search',
					'syria-bot'
				);
				?>
			</button>

		</form>

		<br>

		<table class="widefat striped">

			<thead>

				<tr>

					<th>#</th>

					<th>
						<?php
						esc_html_e(
							'Question',
							'syria-bot'
						);
						?>
					</th>

					<th>
						<?php
						esc_html_e(
							'Count',
							'syria-bot'
						);
						?>
					</th>

					<th>
						<?php
						esc_html_e(
							'Status',
							'syria-bot'
						);
						?>
					</th>

					<th>
						<?php
						esc_html_e(
							'Updated',
							'syria-bot'
						);
						?>
					</th>

					<th>
						<?php
						esc_html_e(
							'Actions',
							'syria-bot'
						);
						?>
					</th>

				</tr>

			</thead>

			<tbody>

			<?php if ( ! empty( $questions ) ) : ?>

				<?php foreach ( $questions as $question ) : ?>

					<tr>

						<td>
							<?php
							echo esc_html(
								$question->id
							);
							?>
						</td>

						<td>
							<?php
							echo esc_html(
								$question->question
							);
							?>
						</td>

						<td>
							<?php
							echo esc_html(
								$question->count
							);
							?>
						</td>

						<td>
							<?php
							echo esc_html(
								$question->status
							);
							?>
						</td>

						<td>
							<?php
							echo esc_html(
								$question->updated_at
							);
							?>
						</td>

						<td>

							<?php

							$review_url = wp_nonce_url(
								admin_url(
									'admin.php?page=syria-bot-questions&change_status=' .
									absint( $question->id ) .
									'&status=reviewed'
								),
								'syria_bot_change_status'
							);

							$answered_url = wp_nonce_url(
								admin_url(
									'admin.php?page=syria-bot-questions&change_status=' .
									absint( $question->id ) .
									'&status=answered'
								),
								'syria_bot_change_status'
							);

							$delete_url = wp_nonce_url(
								admin_url(
									'admin.php?page=syria-bot-questions&delete_question=' .
									absint( $question->id )
								),
								'syria_bot_delete_question'
							);

							?>

							<a
								href="<?php echo esc_url( $review_url ); ?>"
							>
								<?php
								esc_html_e(
									'Review',
									'syria-bot'
								);
								?>
							</a>

							|

							<a
								href="<?php echo esc_url( $answered_url ); ?>"
							>
								<?php
								esc_html_e(
									'Answered',
									'syria-bot'
								);
								?>
							</a>

							|

							<a
								href="<?php echo esc_url( $delete_url ); ?>"
								style="color:red"
								onclick="return confirm('<?php echo esc_attr__( 'Delete question?', 'syria-bot' ); ?>')"
							>
								<?php
								esc_html_e(
									'Delete',
									'syria-bot'
								);
								?>
							</a>

						</td>

					</tr>

				<?php endforeach; ?>

			<?php else : ?>

				<tr>

					<td colspan="6">

						<?php
						esc_html_e(
							'No questions found.',
							'syria-bot'
						);
						?>

					</td>

				</tr>

			<?php endif; ?>

			</tbody>

		</table>

	</div>

	<?php
}

