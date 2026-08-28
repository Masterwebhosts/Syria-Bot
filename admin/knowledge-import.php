<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * حفظ شجرة التصنيفات في جدول قاعدة المعرفة.
 *
 * @param WP_Term $term التصنيف.
 * @return int رقم التصنيف في جدول البوت.
 */
function syria_bot_save_category_tree( $term ) {

	global $wpdb;

	$table = $wpdb->prefix . 'ai_bot_categories';

	$parent_id = 0;

	/*
	 * حفظ التصنيف الأب أولاً.
	 */
	if ( $term->parent ) {

		$parent = get_term(
			$term->parent,
			'category'
		);

		if (
			$parent &&
			! is_wp_error( $parent )
		) {

			$parent_id = syria_bot_save_category_tree(
				$parent
			);
		}
	}

	/*
	 * البحث عن التصنيف الموجود مسبقاً.
	 *
	 * اسم الجدول يتم توليده داخلياً من بادئة WordPress
	 * ولا يأتي من إدخال المستخدم.
	 */
	$exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			'SELECT id FROM ' . $wpdb->prefix . 'ai_bot_categories WHERE category_id = %d',
			absint( $term->term_id )
		)
	);

	if ( $exists ) {
		return (int) $exists;
	}

	/*
	 * إضافة التصنيف.
	 */
	$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$table,
		array(
			'category_id' => absint(
				$term->term_id
			),

			'parent_id' => absint(
				$parent_id
			),

			'name' => sanitize_text_field(
				$term->name
			),

			'slug' => sanitize_title(
				$term->slug
			),

			'description' => sanitize_textarea_field(
				$term->description
			),

			'created_at' => current_time(
				'mysql'
			),

			'updated_at' => current_time(
				'mysql'
			),
		)
	);

	return (int) $wpdb->insert_id;
}


/**
 * AJAX Import.
 */
add_action(
	'wp_ajax_syria_bot_import_batch',
	'syria_bot_import_batch'
);


/**
 * استيراد دفعة من المقالات والصفحات إلى قاعدة المعرفة.
 *
 * @return void
 */
function syria_bot_import_batch() {

	check_ajax_referer(
		'syria_bot_import',
		'nonce'
	);

	if ( ! current_user_can( 'manage_options' ) ) {

		wp_send_json_error(
			array(
				'message' => 'Permission denied',
			)
		);
	}

	global $wpdb;

	$table = $wpdb->prefix . 'ai_bot_knowledge';

	$offset = absint(
		get_option(
			'syria_bot_import_offset',
			0
		)
	);

	$limit = 20;

	/*
	 * جلب المقالات والصفحات المنشورة.
	 */
	$posts = get_posts(
		array(
			'post_type' => array(
				'post',
				'page',
			),

			'post_status' => 'publish',

			'numberposts' => $limit,

			'offset' => $offset,

			'orderby' => 'ID',

			'order' => 'ASC',
		)
	);

	/*
	 * انتهاء الاستيراد.
	 */
	if ( empty( $posts ) ) {

		delete_option(
			'syria_bot_import_offset'
		);

		wp_send_json_success(
			array(
				'finished' => true,

				'message' => 'تم تحديث قاعدة المعرفة بنجاح',
			)
		);
	}

	/*
	 * معالجة كل مقال.
	 */
	foreach ( $posts as $post ) {

		/*
		 * تنظيف محتوى المقال.
		 */
		
			
          $content = apply_filters(
	      'the_content', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	      $post->post_content
           );



		$content = wp_strip_all_tags(
			$content
		);

		/*
		 * التصنيفات.
		 */
		$category_id     = 0;
		$category_name   = '';
		$parent_category = '';

		$categories = wp_get_post_terms(
			$post->ID,
			'category'
		);

		if (
			! empty( $categories ) &&
			! is_wp_error( $categories )
		) {

			$category = $categories[0];

			/*
			 * بناء شجرة التصنيفات.
			 */
			syria_bot_save_category_tree(
				$category
			);

			$category_id = absint(
				$category->term_id
			);

			$category_name = $category->name;

			/*
			 * تحديد التصنيف الأب.
			 */
			if ( $category->parent ) {

				$parent = get_term(
					$category->parent,
					'category'
				);

				if (
					$parent &&
					! is_wp_error( $parent )
				) {

					$parent_category = $parent->name;
				}
			} else {

				$parent_category = $category->name;
			}
		}

		/*
		 * الوسوم.
		 */
		$tags = array();

		$post_tags = get_the_tags(
			$post->ID
		);

		if (
			! empty( $post_tags ) &&
			! is_wp_error( $post_tags )
		) {

			foreach ( $post_tags as $tag ) {

				$tags[] = $tag->name;
			}
		}

		/*
		 * بيانات المقال.
		 */
		$data = array(
			'post_id' => absint(
				$post->ID
			),

			'title' => sanitize_text_field(
				$post->post_title
			),

			'content' => $content,

			'keywords' => '',

			'url' => esc_url_raw(
				get_permalink(
					$post->ID
				)
			),

			'category_id' => $category_id,

			'category_name' => sanitize_text_field(
				$category_name
			),

			'parent_category' => sanitize_text_field(
				$parent_category
			),

			'tags' => sanitize_text_field(
				implode(
					', ',
					$tags
				)
			),

			'updated_at' => current_time(
				'mysql'
			),
		);

		/*
		 * البحث عن المقال الموجود.
		 *
		 * post_id هو المعرف الصحيح للمقال.
		 */
		$exists = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				'SELECT id FROM ' . $wpdb->prefix . 'ai_bot_knowledge WHERE post_id = %d',
				absint( $post->ID )
			)
		);

		/*
		 * تحديث المقال أو إضافته.
		 */
		if ( $exists ) {

			$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$table,
				$data,
				array(
					'post_id' => absint(
						$post->ID
					),
				)
			);

		} else {

			$data['created_at'] = current_time(
				'mysql'
			);

			$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$table,
				$data
			);
		}
	}

	/*
	 * حفظ موضع الدفعة التالية.
	 */
	update_option(
		'syria_bot_import_offset',
		$offset + count( $posts ),
		false
	);

	/*
	 * إرسال نتيجة الدفعة.
	 */
	wp_send_json_success(
		array(
			'finished' => false,

			'count' => count( $posts ),

			'offset' => $offset + count( $posts ),
		)
	);
}