<?php

function advance_login_verify_login(WP_REST_Request $request)
{
  $mobile = get_mobile($request->get_param('mobile'));
  $code = $request->get_param('code');

  $requests = advance_login_get_request($mobile, $code);
  if (count($requests) === 1) {
    $request = $requests[0];
    $created_at = strtotime($request->created_at);
    $diff = time() - $created_at;
    if ($diff > 15 * 60) {
      return new WP_Error('code', __('Invalid verification code', 'advance-login'));
    }

    if (username_exists($mobile)) {
      $user = get_user_by('login', $mobile);
      update_user_meta($user->ID, 'billing_phone', $mobile);
      wp_clear_auth_cookie();
      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user->ID);
      advance_login_delete_requests($mobile);

      $res = array(
        'type' => 'login',
        'redirect_url' => get_permalink(get_option('woocommerce_myaccount_page_id')),
      );
      return new WP_REST_Response($res, 200);
    } else {
      $res = array(
        'type' => 'register',
        'code' => $code,
      );
      return new WP_REST_Response($res, 200);
    }
  } else {
    return new WP_Error('code', __('Invalid verification code', 'advance-login'));
  }
}

function advance_login_verify_and_register(WP_REST_Request $request)
{
  $mobile = get_mobile($request->get_param('mobile'));
  $code = $request->get_param('code');
  $password = $request->get_param('password');
  $first_name = $request->get_param('first_name');
  $last_name = $request->get_param('last_name');

  $requests = advance_login_get_request($mobile, $code);
  if (count($requests) === 1) {
    $request = $requests[0];
    $created_at = strtotime($request->created_at);
    $diff = time() - $created_at;
    if ($diff > 15 * 60) {
      return new WP_Error('code', __('Invalid verification code', 'advance-login'));
    }

    if (username_exists($mobile)) {
      return new WP_Error('code', __('Username already exists', 'advance-login'));
    } else {
      wp_create_user($mobile, $password);
      $user = get_user_by('login', $mobile);
      update_user_meta($user->ID, 'billing_phone', $mobile);
      if (isset($first_name)) {
        update_user_meta($user->ID, 'billing_first_name', sanitize_text_field($first_name));
        update_user_meta($user->ID, 'first_name', sanitize_text_field($first_name));
      }
      if (isset($last_name)) {
        update_user_meta($user->ID, 'billing_last_name', sanitize_text_field($last_name));
        update_user_meta($user->ID, 'last_name', sanitize_text_field($last_name));
      }

      $display_name = isset($first_name) && isset($last_name) ? sanitize_text_field($first_name) . ' ' . sanitize_text_field($last_name) : 'کاربر سایت';
      update_user_meta($user->ID, 'display_name', $display_name);
      update_user_meta($user->ID, 'nickname', $display_name);
      wp_update_user(array('ID' => $user->ID, 'display_name' =>  $display_name));

      wp_clear_auth_cookie();
      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user->ID);
      advance_login_delete_requests($mobile);

      $res = array(
        'type' => 'login',
        'redirect_url' => get_permalink(get_option('woocommerce_myaccount_page_id')),
      );
      return new WP_REST_Response($res, 200);
    }
  } else {
    return new WP_Error('code', __('Invalid verification code', 'advance-login'));
  }
}

function advance_login_get_request($mobile, $code)
{
  global $wpdb;
  $table_name = get_table_name();
  $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE mobile='%s' AND code='%s'", $mobile, $code);
  return $wpdb->get_results($sql);
}
