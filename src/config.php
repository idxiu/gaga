<?php
 return array (
  'apiPageIndex' => '/index.php?action=page.index',
  'apiPageLogin' => '/index.php?action=page.login',
  'apiPageLogout' => '/index.php?action=page.logout',
  'loginPluginId' => '105',
  'apiPageWidget' => '/index.php?action=page.widget',
  'apiSiteLogin' => '/index.php?action=api.site.login&body_format=pb',
  'session_verify_100' => 'http://192.144.153.21:5208/index.php?action=api.session.verify&body_format=pb',
  'session_verify_105' => 'http://127.0.0.1:5207/index.php?action=api.session.verify&body_format=pb',
  'initDB' => './index.php?action=installDB',
  'mail' => 
  array (
    'host' => 'smtp.126.com',
    'SMTPAuth' => true,
    'emailAddress' => 'xxxx@126.com',
    'password' => '',
    'SMTPSecure' => '',
    'port' => 25,
  ),
  'sqlite' => 
  array (
    'sqliteDBPath' => '.',
    'sqliteDBName' => '',
  ),
);
 