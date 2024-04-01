<?php

use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Dashboard']);

require_once '../autoload.php';

$movieService = new MovieService();
$movies = $movieService->getMovies();
$prevPage = isset($_GET['page']) && $_GET['page'] > 1 ? $_GET['page'] - 1 : null;
$nextPage = isset($_GET['page']) && $_GET['page'] < $movies['totalPages'] ? $_GET['page'] + 1 : null;
?>

  <main class="py-5">
    <section class="mb-5">
      <div class="container d-flex justify-content-end gap-2">
        <a href="add-movie.php" class="btn btn-primary">Add Movie</a>
        <a href="add-actor.php" class="btn btn-primary">Add Actor</a>
        <a href="add-format.php" class="btn btn-primary">Add Format</a>
        <a href="import-movies.php" class="btn btn-primary">Import Movies</a>
      </div>
    </section>
      <?php
      if (empty($movies['movies'])) : ?>
        <div class="container">
          <h2 class="text-center m-0">No movies found.</h2>
        </div>
      <?php
      else: ?>
        <section class="mb-4">
          <div class="container">
            <table class="table table-hover mb-0">
              <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Release Date</th>
                <th scope="col">Format</th>
                <th scope="col">Actors</th>
                <th scope="col">Actions</th>
              </tr>
              </thead>
              <tbody>
              <?php
              foreach ($movies['movies'] as $movie): ?>
                <tr>
                  <th scope="row"><?php
                      echo $movie['id']; ?></th>
                  <td><?php
                      echo $movie['name']; ?></td>
                  <td><?php
                      echo $movie['release_date']; ?></td>
                  <td><?php
                      echo $movie['format_name']; ?></td>
                  <td><?php
                      echo $movie['actor_names']; ?></td>
                  <td class="d-flex">
                    <!--                <form method="post" class="me-2">-->
                    <!--                  <button type="submit" class="btn btn-primary disabled">Edit</button>-->
                    <!--                </form>-->
                    <form action="delete-movie.php" method="post">
                      <input type="hidden" name="movie_id" value="<?php
                      echo $movie['id']; ?>">
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              <?php
              endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
        <section>
          <div class="container">
            <nav aria-label="Movies Pagination">
              <ul class="pagination text-center justify-content-center">
                <li class="page-item <?php
                echo $prevPage ?? 'disabled' ?>">
                  <a class="page-link" href="<?php
                  echo isset($prevPage) ? "?page=$prevPage" : '#'; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
                  <?php
                  for ($i = 1; $i <= $movies['totalPages']; $i++) :?>
                    <li class="page-item <?php
                    echo (!isset($_GET['page']) && $i == 1) || (isset($_GET['page']) && $_GET['page'] == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="<?php
                      echo "?page=$i"; ?>"><?php
                          echo $i; ?></a></li>
                  <?php
                  endfor; ?>
                <li class="page-item <?php
                echo $nextPage ?? 'disabled' ?>">
                  <a class="page-link" href="<?php
                  echo isset($nextPage) ? "?page=$nextPage" : '#'; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </section>
      <?php
      endif; ?>
  </main>

<?php
view('footer'); ?>