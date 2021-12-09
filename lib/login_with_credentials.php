<?php

function advance_login_login_with_credentials(WP_REST_Request $request)
{
  $username = $request->get_param('username');
  $password = $request->get_param('password');
  $signon = wp_signon(array('user_login' => $username, 'user_password' => $password, 'remember' => true));
  if (is_wp_error($signon)) {
    return new WP_Error('credentials', __('Invalid credentials', 'advance-login'));
  }

  $res = array(
    'type' => 'login',
    'redirect_url' => get_permalink(get_option('woocommerce_myaccount_page_id')),
  );
  return new WP_REST_Response($res, 200);
}
