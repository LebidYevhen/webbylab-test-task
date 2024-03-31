<?php

session_start();

require_once '../src/libs/helpers.php';

if (empty($_SESSION['user'])) {
    redirectTo('/login.php');
}

view('header', ['title' => 'Dashboard']);

require_once '../autoload.php';
?>

  <main class="py-5">
    <section class="mb-5">
      <div class="container text-end">
        <a href="add-movie.php" class="btn btn-primary">Add Movie</a>
      </div>
    </section>
    <section class="mb-4">
      <div class="container">
        <table class="table table-hover mb-0">
          <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">First</th>
            <th scope="col">Last</th>
            <th scope="col">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <th scope="row">1</th>
            <td>Mark</td>
            <td>Otto</td>
            <td class="d-flex">
              <form class="me-2">
                <button type="submit" class="btn btn-primary">Edit</button>
              </form>
              <form>
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          <tr>
            <th scope="row">2</th>
            <td>Jacob</td>
            <td>Thornton</td>
            <td class="d-flex">
              <form class="me-2">
                <button type="submit" class="btn btn-primary">Edit</button>
              </form>
              <form>
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          <tr>
            <th scope="row">3</th>
            <td>Larry the Bird</td>
            <td>Last</td>
            <td class="d-flex">
              <form class="me-2">
                <button type="submit" class="btn btn-primary">Edit</button>
              </form>
              <form>
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </section>
    <section>
      <div class="container">
        <nav aria-label="Page navigation example">
          <ul class="pagination text-center">
            <li class="page-item disabled">
              <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </section>
  </main>

<?php
view('footer'); ?>