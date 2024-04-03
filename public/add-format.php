<?php

use WebbyLab\Format;
use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Add a new format']);

require_once '../autoload.php';

$movieService = new MovieService();
$formats = $movieService->getFormats();

if (isPostRequest()) {
    $format = new Format();
    $validate = $format->create();
}
?>

  <main class="py-5">
      <?php
      if (!empty($_SESSION['successStatus']) && isset($_SESSION['successStatus']['message'])) : ?>
        <div class="container mb-5">
          <h2 class="text-center m-0 text-success"><?php
              echo $_SESSION['successStatus']['message'];
              unset($_SESSION['successStatus']);
              ?></h2>
        </div>
      <?php
      endif; ?>
    <div class="container">
      <form action="add-format.php" method="post">
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
        <button type="submit" class="btn btn-primary">Create</button>
      </form>
    </div>
      <?php
      if (!empty($formats)): ?>
        <div class="container mt-5">
          <h2>Formats</h2>
          <section>
            <table class="table table-hover mb-0">
              <thead>
              <tr>
                <th scope="col">Name</th>
              </tr>
              </thead>
              <tbody>
              <?php
              foreach ($formats as $format): ?>
                <tr>
                  <th scope="row"><?php
                      echo $format['name']; ?></th>
                </tr>
              <?php
              endforeach; ?>
              </tbody>
            </table>
          </section>
        </div>
      <?php
      endif; ?>
  </main>

<?php
view('footer'); ?>