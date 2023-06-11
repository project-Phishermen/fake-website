<?php

$db_host = 'localhost';
$db_user = '';
$db_pssw = '';
$db_name = '';
$db_port = 3306;
$db_socket = '/run/mysqld/mysqld.sock';

$db = mysqli_connect($db_host, $db_user, $db_pssw, $db_name);

if (!$db) {
    die("Connection failure");
}

?>
