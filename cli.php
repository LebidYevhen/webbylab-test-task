<?php

$sqlFile = file_get_contents('./database.sql');

try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=webbylab", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec($sqlFile);
    print("The tables have been created successfully.\n");
} catch (PDOException $e) {
    echo $e->getMessage();
}