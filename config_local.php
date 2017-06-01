<?php
/* Database cofiguration */
global $server_URL, $db_server, $db_name, $db_uname, $db_pass;
$GLOBALS['global_database'] = 'php_intranet_uniformsourcing_new';
$server_URL = "http://127.0.0.1:4569";  //Server address needed for sending sample request email 
$db_server = "localhost";
$db_name = $GLOBALS['global_database'];                          // database name
$db_uname= "globaluniformuser";                              // username to connect to database
$db_pass= "globaluniformpassword";                                // password of username to connecto to database



/* Live Configuration */
/*
$server_URL = "http://internal.uniformsourcing.com";  //Server address needed for sending sample request $
$db_server = "74.80.222.58";
$db_name = "php_intranet_uniformsourcing";                          // database name
$db_uname= "globaluniformuser";                              // username to connect to database
$db_pass= "globaluniformpassword";                                // password of username to connecto to $
*/