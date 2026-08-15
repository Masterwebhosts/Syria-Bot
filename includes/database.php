<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * إنشاء جداول قاعدة بيانات Syria Bot
 */
function syria_bot_create_database() {


    global $wpdb;


    $charset = $wpdb->get_charset_collate();



    /*
    |--------------------------------------------------------------------------
    | جدول قاعدة المعرفة
    |--------------------------------------------------------------------------
    */


    $knowledge_table = $wpdb->prefix . 'ai_bot_knowledge';


    $sql1 = "CREATE TABLE IF NOT EXISTS {$knowledge_table} (

        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

        post_id bigint(20) unsigned NOT NULL,

        title text NOT NULL,

        content longtext NOT NULL,

        keywords text NULL,

        url text NULL,

        category_id bigint(20) unsigned NOT NULL DEFAULT 0,

        category_name varchar(255) DEFAULT NULL,

        parent_category varchar(255) DEFAULT NULL,

        tags text NULL,

        created_at datetime NOT NULL,

        updated_at datetime NOT NULL,


        PRIMARY KEY (id),

        UNIQUE KEY post_id (post_id),

        KEY category_id (category_id)

    ) {$charset};";




    /*
    |--------------------------------------------------------------------------
    | جدول الأسئلة الواردة
    |--------------------------------------------------------------------------
    */


    $questions_table = $wpdb->prefix . 'ai_bot_questions';


    $sql2 = "CREATE TABLE IF NOT EXISTS {$questions_table} (

        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

        question text NOT NULL,

        normalized_question text NULL,

        count int(11) NOT NULL DEFAULT 1,

        ip varchar(100) DEFAULT NULL,

        user_agent text NULL,

        status varchar(50) NOT NULL DEFAULT 'new',

        linked_post_id bigint(20) unsigned NOT NULL DEFAULT 0,

        created_at datetime NOT NULL,

        updated_at datetime NOT NULL,


        PRIMARY KEY (id),

        KEY status (status),

        KEY linked_post_id (linked_post_id)

    ) {$charset};";





    /*
    |--------------------------------------------------------------------------
    | جدول شجرة التصنيفات
    |--------------------------------------------------------------------------
    */


    $categories_table = $wpdb->prefix . 'ai_bot_categories';


    $sql3 = "CREATE TABLE IF NOT EXISTS {$categories_table} (

        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

        category_id bigint(20) unsigned NOT NULL,

        parent_id bigint(20) unsigned NOT NULL DEFAULT 0,

        name varchar(255) NOT NULL,

        slug varchar(255) NOT NULL,

        description text NULL,

        created_at datetime NOT NULL,

        updated_at datetime NOT NULL,


        PRIMARY KEY (id),

        KEY category_id (category_id),

        KEY parent_id (parent_id)

    ) {$charset};";




    /*
    |--------------------------------------------------------------------------
    | تنفيذ الإنشاء
    |--------------------------------------------------------------------------
    */


    $wpdb->query( $sql1 );

    $wpdb->query( $sql2 );

    $wpdb->query( $sql3 );


}