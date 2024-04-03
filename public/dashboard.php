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
$currentPage = $_GET['page'] ?? 1;
$prevPage = ($currentPage > 1) ? $currentPage - 1 : false;
$nextPage = ($currentPage < $movies['totalPages']) ? $currentPage + 1 : false;
?>

  <main class="py-5">
      <?php
      if (!empty($_SESSION['successStatus'])) : ?>
        <div class="container mb-5">
          <h2 class="text-center m-0 text-success"><?php
              echo $_SESSION['successStatus']['message']; ?>
          </h2>
            <?php
            unset($_SESSION['successStatus']); ?>
        </div>
      <?php
      endif; ?>
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
                  <td>
                    <button type="submit" class="btn btn-danger delete-movie" data-bs-toggle="modal"
                            data-bs-target="#delete-movie-<?php
                            echo $movie['id']; ?>">Delete
                    </button>
                  </td>
                </tr>
                <!-- Modal -->
                <div class="modal fade" id="delete-movie-<?php
                echo $movie['id']; ?>" tabindex="-1" aria-labelledby="ModalLabel<?php
                echo $movie['id']; ?>"
                     aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h2 class="modal-title fs-5" id="ModalLabel<?php
                        echo $movie['id']; ?>">Are you sure you want to delete <b><?php
                                echo $movie['name']; ?></b> movie?</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <form action="delete-movie.php" method="post" class="delete-movie-form">
                          <input type="hidden" name="movie_id" value="<?php
                          echo $movie['id']; ?>">
                          <input type="hidden" name="page" value="<?php
                          echo $_GET['page']; ?>">
                          <button type="submit" class="btn btn-danger" data-bs-toggle="modal-
                      <?php
                          echo $movie['id']; ?>"
                                  data-bs-target="#delete-movie-<?php
                                  echo $movie['id']; ?>">Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              <?php
              endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>
          <?php
          if ($movies['totalPages'] > 1): ?>
            <section>
              <div class="container">
                <nav aria-label="Movies Pagination">
                  <ul class="pagination text-center justify-content-center">
                    <li class="page-item <?php
                    echo empty($prevPage) ? 'disabled' : ''; ?>">
                      <a class="page-link" href="<?php
                      echo !empty($prevPage) ? "?page=$prevPage" : '#'; ?>" aria-label="Previous">
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
                    echo empty($nextPage) ? 'disabled' : ''; ?>">
                      <a class="page-link" href="<?php
                      echo !empty($nextPage) ? "?page=$nextPage" : '#'; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </section>
          <?php
          endif; ?>
      <?php
      endif; ?>
  </main>

<?php
view('footer'); ?>