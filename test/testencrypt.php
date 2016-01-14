<?php
$cost = 10;
$salt = strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.');
$salt = sprintf("$2y$%02d$", $cost).$salt;

$pass = '1234';

header("Content-Type: text/plain; charset=utf8");

$hash = crypt($pass, $salt);
echo "current: ".$hash."\r\n";
echo "current: ".crypt($pass, $hash)."\r\n";

$pass_hash = password_hash($pass, PASSWORD_DEFAULT);
echo "password_hash: ".$pass_hash."\r\n";
echo "password_hash: ".crypt($pass, $pass_hash)."\r\n";
echo "password_hash: ".password_verify($pass, $pass_hash)."\r\n";
?>
