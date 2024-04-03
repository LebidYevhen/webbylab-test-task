<?php

use WebbyLab\Actor;
use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Add a new actor']);

require_once '../autoload.php';

$movieService = new MovieService();
$actors = $movieService->getActors();

if (isPostRequest()) {
    $actor = new Actor();
    $validate = $actor->create();
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
      <form action="add-actor.php" method="post">
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
          <label for="surname" class="form-label">Surname</label>
          <input type="text" class="form-control" id="surname" name="surname" value="<?php
          echo $validate['data']['surname'] ?? ''; ?>">
            <?php
            if (!empty($validate['errors']['surname'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['surname'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
      </form>
    </div>
      <?php
      if (!empty($actors)): ?>
        <div class="container mt-5">
          <h2>Actors</h2>
          <section>
            <table class="table table-hover mb-0">
              <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Surname</th>
              </tr>
              </thead>
              <tbody>
              <?php
              foreach ($actors as $actor): ?>
                <tr>
                  <th scope="row"><?php
                      echo $actor['name']; ?></th>
                  <td><?php
                      echo $actor['surname']; ?></td>
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