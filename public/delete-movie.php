<?php

use WebbyLab\Movie;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

require_once '../autoload.php';

if (isPostRequest()) {
    $movie = new Movie();
    $movie->delete();
}
