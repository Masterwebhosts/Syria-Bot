<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
 * Ø¥Ù†Ø´Ø§Ø¡ Ù‚ÙˆØ§Ø¦Ù… Ø§Ù„Ø¥Ø¯Ø§Ø±Ø©
 */

function syria_bot_admin_menu(){

    add_menu_page(

        __('Syria Bot', 'Syria-Bot'),

        __('Syria Bot', 'Syria-Bot'),

        'manage_options',

        'Syria-Bot',

        'syria_bot_knowledge_page',

        'dashicons-format-chat',

        30

    );


    add_submenu_page(

        'Syria-Bot',

        __('Knowledge Base', 'Syria-Bot'),

        __('Knowledge Base', 'Syria-Bot'),

        'manage_options',

        'Syria-Bot',

        'syria_bot_knowledge_page'

    );


    add_submenu_page(

        'Syria-Bot',

        __('Incoming Questions', 'Syria-Bot'),

        __('Questions', 'Syria-Bot'),

        'manage_options',

        'syria-bot-questions',

        'syria_bot_questions_page'

    );


    add_submenu_page(

        'Syria-Bot',

        __('Settings', 'Syria-Bot'),

        __('Settings', 'Syria-Bot'),

        'manage_options',

        'syria-bot-settings',

        'syria_bot_settings_page'

    );


}


add_action(
    'admin_menu',
    'syria_bot_admin_menu'
);
