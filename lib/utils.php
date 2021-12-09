<?php

function generate_code()
{
  return strval(random_int(10000, 99999));
}

function get_table_name()
{
  global $wpdb;
  return $wpdb->prefix . "advance_login";
}

function is_valid_mobile($input)
{
  $regex = '/^(09|\+989|9)[0-9]{9}$|^00([1-9]{2})[0-9]{10}$/';
  return !!preg_match($regex, $input);
}

function is_valid_code($input)
{
  return strlen($input) === 5;
}

function get_mobile($input)
{
  switch (strlen($input)) {
    case 10:
      return "0{$input}";
    case 13:
      return str_replace("+98", "0", $input);
    default:
      return $input;
  }
}
