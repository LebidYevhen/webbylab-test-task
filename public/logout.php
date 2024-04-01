<?php

use WebbyLab\User;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

require_once '../autoload.php';

$user = new User();
$user->logout();