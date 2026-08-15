<?php
/*
Plugin Name: Syria Bot
Plugin URI: https://tradesphare.com/blog/syria-bot/
Description: AI customer support chatbot powered by your WordPress content without external AI services.
Version: 1.1.0
Author: Syria Bot
Text Domain: syria-bot
Domain Path: /languages
*/


if ( ! defined('ABSPATH') ) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Constants
|--------------------------------------------------------------------------
*/

define(
    'SYRIA_BOT_VERSION',
    '1.1.0'
);


define(
    'SYRIA_BOT_PATH',
    plugin_dir_path(__FILE__)
);


define(
    'SYRIA_BOT_URL',
    plugin_dir_url(__FILE__)
);



/*
|--------------------------------------------------------------------------
| Translation
|--------------------------------------------------------------------------
*/

function syria_bot_load_textdomain(){

    load_plugin_textdomain(
        'syria-bot',
        false,
        dirname(
            plugin_basename(__FILE__)
        ) . '/languages'
    );

}

add_action(
    'plugins_loaded',
    'syria_bot_load_textdomain'
);




/*
|--------------------------------------------------------------------------
| Core Files
|--------------------------------------------------------------------------
*/

require_once SYRIA_BOT_PATH . 'includes/database.php';

require_once SYRIA_BOT_PATH . 'includes/questions.php';

require_once SYRIA_BOT_PATH . 'includes/search.php';

require_once SYRIA_BOT_PATH . 'includes/chatbot.php';

require_once SYRIA_BOT_PATH . 'includes/widget.php';

require_once SYRIA_BOT_PATH . 'includes/auto-category.php';


/*
|--------------------------------------------------------------------------
| Admin Files
|--------------------------------------------------------------------------
*/

function syria_bot_load_admin_files(){


    if ( ! is_admin() ) {

        return;

    }


    require_once SYRIA_BOT_PATH . 'admin/menu.php';

    require_once SYRIA_BOT_PATH . 'admin/knowledge.php';

    require_once SYRIA_BOT_PATH . 'admin/knowledge-import.php';

    require_once SYRIA_BOT_PATH . 'admin/settings.php';

    require_once SYRIA_BOT_PATH . 'admin/questions.php';


}


add_action(
    'plugins_loaded',
    'syria_bot_load_admin_files'
);





/*
|--------------------------------------------------------------------------
| Frontend Assets
|--------------------------------------------------------------------------
*/

function syria_bot_front_assets(){


    wp_enqueue_style(

        'syria-bot-style',

        SYRIA_BOT_URL . 'assets/style.css',

        array(),

        SYRIA_BOT_VERSION

    );



    wp_enqueue_script(

        'syria-bot-chat',

        SYRIA_BOT_URL . 'assets/chat.js',

        array('jquery'),

        SYRIA_BOT_VERSION,

        true

    );



    wp_localize_script(

        'syria-bot-chat',

        'syriaBot',

        array(

            'ajax_url' => admin_url(
                'admin-ajax.php'
            ),


            'nonce' => wp_create_nonce(
                'syria_bot_nonce'
            )

        )

    );


}


add_action(
    'wp_enqueue_scripts',
    'syria_bot_front_assets'
);






/*
|--------------------------------------------------------------------------
| Admin JavaScript
|--------------------------------------------------------------------------
*/

function syria_bot_admin_assets( $hook ) {


    if (
        strpos(
            $hook,
            'syria-bot'
        ) === false
    ) {

        return;

    }



    wp_enqueue_script(

        'syria-bot-admin',

        SYRIA_BOT_URL . 'assets/admin.js',

        array(
            'jquery'
        ),

        SYRIA_BOT_VERSION,

        true

    );



    wp_localize_script(

        'syria-bot-admin',

        'syriaBotAdmin',

        array(

            'ajax_url' => admin_url(
                'admin-ajax.php'
            ),


            'nonce' => wp_create_nonce(
                'syria_bot_import'
            )

        )

    );


}


add_action(
    'admin_enqueue_scripts',
    'syria_bot_admin_assets'
);






/*
|--------------------------------------------------------------------------
| Activation
|--------------------------------------------------------------------------
*/

function syria_bot_activate(){


    update_option(
        'syria_bot_needs_database',
        true
    );


}


register_activation_hook(
    __FILE__,
    'syria_bot_activate'
);







/*
|--------------------------------------------------------------------------
| Database Check
|--------------------------------------------------------------------------
*/

function syria_bot_check_database(){



    $current_version = get_option(
        'syria_bot_db_version',
        ''
    );



    if (

        get_option(
            'syria_bot_needs_database'
        )

        ||

        $current_version !== SYRIA_BOT_VERSION


    ) {



        if(
            function_exists(
                'syria_bot_create_database'
            )
        ){


            syria_bot_create_database();



            update_option(

                'syria_bot_db_version',

                SYRIA_BOT_VERSION

            );


        }



        delete_option(
            'syria_bot_needs_database'
        );


    }



}


add_action(
    'admin_init',
    'syria_bot_check_database'
);