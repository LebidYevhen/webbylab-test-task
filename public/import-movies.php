<?php

use WebbyLab\Actor;
use WebbyLab\Database;
use WebbyLab\Movie;
use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Import movies']);

require_once '../autoload.php';

$movieService = new MovieService();

if (isPostRequest()) {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $movieService->importMovies();
        redirectTo('/import-movies.php');
    } else {
        echo "Error uploading file.";
    }
}
?>

  <main class="py-5">
    <div class="container">
      <form action="import-movies.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="file" class="form-label">Name</label>
          <input type="file" class="form-control" id="file" name="file">
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
      </form>
    </div>
  </main>

<?php
view('footer'); ?>