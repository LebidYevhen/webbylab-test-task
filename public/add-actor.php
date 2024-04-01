<?php

use WebbyLab\Actor;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Add a new actor']);

require_once '../autoload.php';

if (isPostRequest()) {
    $actor = new Actor();
    $validate = $actor->create();
}
?>

  <main class="py-5">
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
  </main>

<?php
view('footer'); ?>