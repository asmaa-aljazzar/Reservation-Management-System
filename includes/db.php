<?php

require_once __DIR__.'/../config/config.php';

try {
    $db = new PDO ($dsn, $dbUser, $dbPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected Successfully";
}catch (PDOException $e){
    error_log($e->getMessage());
    exit ();
}

function prepare(string $sql)
{
    global $db;
    return $db->prepare ($sql);
}