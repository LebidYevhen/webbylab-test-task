<?php

namespace WebbyLab;

use WebbyLab\Services\MovieService;

class Movie
{
    private MovieService $movieService;
    private Database $database;

    public function __construct()
    {
        $this->movieService = new MovieService();
        $this->database = new Database();
    }

    public function create()
    {
        $fields = [
          'name' => $_POST['name'],
          'release_date' => $_POST['release_date'],
          'format' => $_POST['format'],
          'actors' => $_POST['actors'] ?? []
        ];

        $validator = $this->movieService->validateCreate();

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $this->movieService->handleCreate($data);

            session_start();
            $_SESSION['successStatus'] = [
              'success' => true,
              'message' => '1 Movie created.'
            ];

            redirectTo('/add-movie.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }

    public function delete()
    {
        $fields = [
          'movie_id' => $_POST['movie_id'],
        ];

        $validator = $this->movieService->validateDelete();

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();
            $movie = $this->getMovieById($data['movie_id']);

            session_start();
            $_SESSION['successStatus'] = [
              'success' => true,
              'message' => "Movie {$movie['name']} deleted."
            ];

            $this->movieService->handleDelete($data);

            $redirectPath = !empty($_POST['page']) && intval($_POST['page']) ? '/dashboard.php'.'?page='.intval(
                $_POST['page']
              ) : '/dashboard.php';

            redirectTo($redirectPath);
        }
    }

    public function getMovieById($movieId)
    {
        return $this->database->query('SELECT * from movies WHERE id = :id', [
          'id' => $movieId
        ])->find();
    }
}