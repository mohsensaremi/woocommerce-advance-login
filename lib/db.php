<?php
function advance_login_db()
{
  global $wpdb;

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE " . get_table_name() . " (
  id mediumint(12) NOT NULL AUTO_INCREMENT,
  mobile varchar(15) NOT NULL,
  code varchar(10) NOT NULL,
  sms_provider_data varchar(200),
  created_at datetime NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

  var_dump($sql);

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}
