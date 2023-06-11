<?php

$db_host = 'localhost';
$db_user = 'admin';
$db_pssw = 'adminphisher';
$db_name = 'otronic_cridentials_db';
$db_port = 3306;
$db_socket = '/run/mysqld/mysqld.sock';

$db = mysqli_connect($db_host, $db_user, $db_pssw, $db_name);

if (!$db) {
    die("Connection failure");
}

?>
