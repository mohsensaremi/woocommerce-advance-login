<?php

class FarazSms implements ISmsProvider
{
  public function sendVerificationCode($mobile, $code)
  {
    global $advance_login_secrets;
    $apiKey = $advance_login_secrets['faraz_sms']['api_key'];

    $client = new \IPPanel\Client($apiKey);
    return $client->sendPattern(
      $advance_login_secrets['faraz_sms']['pattern_code'],
      $advance_login_secrets['faraz_sms']['originator'],
      $mobile,
      array(
        'verification-code' => $code
      )
    );
  }
}
