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
    $validate = $movieService->importMovies();
}
?>

  <main class="py-5">
      <?php
      if (!empty($_SESSION['successStatus'])) : ?>
        <div class="container">
            <?php
            foreach ($_SESSION['successStatus']['messages'] as $message): ?>
              <h2 class="text-center m-0 text-success"><?php
                  echo $message; ?>
              </h2>
            <?php
            endforeach; ?>
            <?php
            unset($_SESSION['successStatus']); ?>
        </div>
      <?php
      endif; ?>
    <div class="container">
      <form action="import-movies.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="file" class="form-label">Name</label>
          <input type="file" class="form-control" id="file" name="file">
            <?php
            if (!empty($validate['errors']['file'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['file'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
      </form>
    </div>
  </main>

<?php
view('footer'); ?>