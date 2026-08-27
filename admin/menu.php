<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
 * Ø¥Ù†Ø´Ø§Ø¡ Ù‚ÙˆØ§Ø¦Ù… Ø§Ù„Ø¥Ø¯Ø§Ø±Ø©
 */

function syria_bot_admin_menu(){

    add_menu_page(

        __('Syria Bot', 'syria-bot'),

        __('Syria Bot', 'syria-bot'),

        'manage_options',

        'syria-bot',

        'syria_bot_knowledge_page',

        'dashicons-format-chat',

        30

    );


    add_submenu_page(

        'syria-bot',

        __('Knowledge Base', 'syria-bot'),

        __('Knowledge Base', 'syria-bot'),

        'manage_options',

        'syria-bot',

        'syria_bot_knowledge_page'

    );


    add_submenu_page(

        'syria-bot',

        __('Incoming Questions', 'syria-bot'),

        __('Questions', 'syria-bot'),

        'manage_options',

        'syria-bot-questions',

        'syria_bot_questions_page'

    );


    add_submenu_page(

        'syria-bot',

        __('Settings', 'syria-bot'),

        __('Settings', 'syria-bot'),

        'manage_options',

        'syria-bot-settings',

        'syria_bot_settings_page'

    );


}


add_action(
    'admin_menu',
    'syria_bot_admin_menu'
);

