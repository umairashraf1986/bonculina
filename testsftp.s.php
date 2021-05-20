<?php

$server = 'localhost';
$port = '22';
$username = 'synceshop.bonculina.se';
$passwd = 'fg3(325%#CTG32';
 
// connect
$connection = ssh2_connect($server, $port);
if (ssh2_auth_password($connection, $username, $passwd)) {
// initialize sftp
$sftp = ssh2_sftp($connection);
 
/* Upload file
echo "Connection successful, uploading file now..."."n";
 
$file = 'test.txt';
$contents = file_get_contents($file);
file_put_contents("ssh2.sftp://{$sftp}/{$file}", $contents);
 */
} else {
echo "Unable to authenticate with server"."n";
}