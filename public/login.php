<?php

use WebbyLab\User;

session_start();

require_once '../src/libs/helpers.php';

if (!empty($_SESSION['user'])) {
    redirectTo('/dashboard.php');
}

require_once '../autoload.php';

view('header', ['title' => 'Login']);

if (isPostRequest()) {
    $register = new User();
    $validate = $register->login();
}
?>

  <main class="py-5">
    <div class="container">
      <form action="login.php" method="post">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php
          echo $validate['data']['email'] ?? ''; ?>">
            <?php
            if (!empty($validate['errors']['email'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['email'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password">
            <?php
            if (!empty($validate['errors']['password'])): ?>
              <div class="form-text text-danger"><?php
                  echo $validate['errors']['password'][0]; ?></div>
            <?php
            endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </main>

<?php
view('footer'); ?>