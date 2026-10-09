<?php
/**
 * Check Login
 *
 * Moved from WPCode snippet #29449 (location: everywhere). Code unchanged.
 */

function enqueue_login_check_script_fast() {

    wp_register_script(
        'login-check-inline',
        false,
        [],
        null,
        true
    );

    wp_enqueue_script('login-check-inline');

    wp_add_inline_script(
        'login-check-inline',
        'window.userLoginStatus = ' . wp_json_encode([
            'isLoggedIn' => is_user_logged_in()
        ]) . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_login_check_script_fast');

