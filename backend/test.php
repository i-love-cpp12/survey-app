<?php
require_once("token.php");
require_once("conn.php");

$pdo = createConnection();
print_r(setToken($pdo));
$pdo = null;
echo phpinfo();
// print_r(headers_list());