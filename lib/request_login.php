<?php

function advance_login_request_login(WP_REST_Request $request)
{
  $mobile = get_mobile($request->get_param('mobile'));
  $code = generate_code();
  // $code = '12345';

  advance_login_delete_requests($mobile);
  $provider = new FarazSms();
  $sms_provider_data = $provider->sendVerificationCode($mobile, $code);
  advance_login_store_request($mobile, $code, $sms_provider_data);
  // advance_login_store_request($mobile, $code, ['x' => 'b']);

  $res = array(
    'mobile' => $mobile,
  );

  return new WP_REST_Response($res, 200);
}

function advance_login_store_request($mobile, $code, $sms_provider_data)
{
  global $wpdb;
  $table_name = get_table_name();
  return $wpdb->insert($table_name, array(
    'mobile' => $mobile,
    'code' => $code,
    'created_at' => date('c'),
    'sms_provider_data' => json_encode($sms_provider_data),
  ));
}

function advance_login_delete_requests($mobile)
{
  global $wpdb;
  $table_name = get_table_name();
  return $wpdb->delete($table_name, array('mobile' => $mobile));
}
