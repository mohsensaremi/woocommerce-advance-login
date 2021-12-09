<?php

interface ISmsProvider
{
  public function sendVerificationCode($mobile, $code);
}
