<?php

use WebbyLab\Services\MovieService;

session_start();

require_once '../src/libs/helpers.php';

view('header', ['title' => 'Search']);

require_once '../autoload.php';

if (isset($_GET['page']) && !intval($_GET['page'])) {
    $query = http_build_query([
      's' => $_GET['s'] ?? '',
    ]);
    redirectTo('/search.php?'.$query);
}

if (isset($_GET['page']) && !preg_match('/^\d+$/', $_GET['page'])) {
    $number = preg_replace('/^(\d*).*$/', '$1', $_GET['page']);
    $query = http_build_query([
      's' => $_GET['s'] ?? '',
      'page' => $number,
    ]);
    redirectTo('/search.php?'.$query);
}

$searchTerm = $_GET['s'] ?? null;
$movieService = new MovieService();
$movies = $movieService->getMovies($searchTerm);
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$prevPage = ($currentPage > 1) ? $currentPage - 1 : false;
$nextPage = ($currentPage < $movies['totalPages']) ? $currentPage + 1 : false;
?>

<main class="py-5">
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
              </tr>
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
                    echo !empty($prevPage) ? "?s=$searchTerm&page=$prevPage" : '#'; ?>" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                    <?php
                    for ($i = 1; $i <= $movies['totalPages']; $i++) :?>
                      <li class="page-item <?php
                      echo (!$currentPage && $i == 1) || (!empty($currentPage) && $currentPage == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php
                        echo "?s=$searchTerm&page=$i"; ?>"><?php
                            echo $i; ?></a></li>
                    <?php
                    endfor; ?>
                  <li class="page-item <?php
                  echo empty($nextPage) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php
                    echo !empty($nextPage) ? "?s=$searchTerm&page=$nextPage" : '#'; ?>" aria-label="Next">
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
