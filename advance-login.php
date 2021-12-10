<?php

/**
 * Plugin Name:       ADVANCE LOGIN
 * Description:       Login and register using mobile sms verification.
 * Version:           1.0.0
 * Author:            Mohsen Sareminia
 * Author URI:        https://github.com/mohsensaremi
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       advance-login
 * Domain Path:       /languages
 */

require_once "vendor/autoload.php";
require_once "secrets.php";
require_once "lib/utils.php";
require_once "lib/db.php";
require_once "lib/sms-provider/ISmsProvider.php";
require_once "lib/sms-provider/FarazSms.php";
require_once "lib/request_login.php";
require_once "lib/validate_login.php";
require_once "lib/login_with_credentials.php";

function advance_login_plugin_init()
{
  load_plugin_textdomain('advance-login', false, 'advance-login/languages');
}
add_action('init', 'advance_login_plugin_init');

register_activation_hook(__FILE__, 'advance_login_db');

add_action('rest_api_init', function () {
  register_rest_route('advance-login/v1', '/request-login', array(
    'methods' => 'POST',
    'callback' => 'advance_login_request_login',
    'args' => array(
      'mobile' => array(
        'validate_callback' => function ($param) {
          return is_valid_mobile($param);
        }
      ),
    ),
  ));
  register_rest_route('advance-login/v1', '/verify-login', array(
    'methods' => 'POST',
    'callback' => 'advance_login_verify_login',
    'args' => array(
      'mobile' => array(
        'validate_callback' => function ($param) {
          return is_valid_mobile($param);
        }
      ),
      'code' => array(
        'validate_callback' => function ($param) {
          return is_valid_code($param);
        }
      ),
    ),
  ));
  register_rest_route('advance-login/v1', '/verify-and-register', array(
    'methods' => 'POST',
    'callback' => 'advance_login_verify_and_register',
    'args' => array(
      'mobile' => array(
        'validate_callback' => function ($param) {
          return is_valid_mobile($param);
        }
      ),
      'code' => array(
        'validate_callback' => function ($param) {
          return is_valid_code($param);
        }
      ),
    ),
  ));
  register_rest_route('advance-login/v1', '/login-with-credentials', array(
    'methods' => 'POST',
    'callback' => 'advance_login_login_with_credentials',
    'args' => array(
      'username' => array(
        'required' => true,
        'type' => 'string',
      ),
      'password' => array(
        'required' => true,
        'type' => 'string',
      ),
    ),
  ));
});

// enqueue
function advance_login_style_script()
{
  if (!is_admin()) {
    wp_enqueue_script("advance-login-script", plugin_dir_url(__FILE__) . "/assets/js/index.js", array("jquery-lib"), "1.0.0", true);
  }
}
add_action('wp_enqueue_scripts', 'advance_login_style_script');
