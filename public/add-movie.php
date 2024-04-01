<?php

use WebbyLab\Movie;
use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Add a new movie']);

require_once '../autoload.php';

$movieService = new MovieService();
$formats = $movieService->getFormats();
$actors = $movieService->getActors();

if (isPostRequest()) {
    $movie = new Movie();
    $validate = $movie->create();
}
?>

  <main class="py-5">
    <div class="container">
      <form action="add-movie.php" method="post">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" id="name" name="name" value="<?php
          echo $validate['data']['name'] ?? ''; ?>">
            <?php
            if (!empty($validate['errors']['name'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['name'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <div class="mb-3">
          <label for="release_date" class="form-label">Release Date</label>
          <input type="date" class="form-control" id="release_date" name="release_date" value="<?php
          echo $validate['data']['release_date'] ?? ''; ?>">
            <?php
            if (!empty($validate['errors']['release_date'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['release_date'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <div class="mb-3">
          <label for="actors" class="form-label">Actors</label>
          <select class="form-select" name="actors[]" id="actors" multiple aria-label="Multiple select example">
              <?php
              foreach ($actors as $actor): ?>
                <option <?php
                echo isset($_POST['actors']) && in_array($actor['id'], $_POST['actors']) ? 'selected' : ''; ?>
                        value="<?php
                        echo $actor['id']; ?>"><?php
                    echo "{$actor['name']} {$actor['surname']}"; ?></option>
              <?php
              endforeach; ?>
          </select>
            <?php
            if (!empty($validate['errors']['actors'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['actors'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <div class="mb-3">
          <label for="format" class="form-label">Format</label>
          <select class="form-select" name="format" id="format" aria-label="Multiple select example">
            <option value="" selected>Select Format</option>
              <?php
              foreach ($formats as $format): ?>
                <option <?php
                echo isset($_POST['format']) && $format['id'] === $_POST['format'] ? 'selected' : ''; ?>
                        value="<?php
                        echo $format['id']; ?>"><?php
                    echo $format['name']; ?></option>
              <?php
              endforeach; ?>
          </select>
            <?php
            if (!empty($validate['errors']['format'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['format'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </main>

<?php
view('footer'); ?>