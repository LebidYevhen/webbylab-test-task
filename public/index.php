<?php

require_once '../src/libs/helpers.php';

view('header', ['title' => 'Home']);

require_once '../autoload.php';
?>


<main class="py-5">
  <div class="container">
    <h1>Home</h1>
  </div>
</main>

<?php
view('footer'); ?>
