<?php
// db.php

// URLがlocalhostかどうかで条件分岐
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $servername = "db"; // Docker Composeのサービス名
    $username = "user";
    $password = "password";
    $dbname = "mydatabase";
} else {
    $servername = "mysql1007.conoha.ne.jp";
    $username = "ti0bv_syabeclip";
    $password = "kS8Uzxwuf_uQ";
    $dbname = "ti0bv_syabeclip";
}

try {
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>