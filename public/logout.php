<?php

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

require_once '../autoload.php';

use WebbyLab\User;

$user = new User();
$user->logout();