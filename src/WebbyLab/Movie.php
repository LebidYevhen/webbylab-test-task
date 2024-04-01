<?php

namespace WebbyLab;

use WebbyLab\Services\MovieService;

class Movie
{
    private MovieService $movieService;

    public function __construct()
    {
        $this->movieService = new MovieService();
    }

    public function create()
    {
        $releaseDate = strtotime($_POST['release_date']);

        $fields = [
          'name' => $_POST['name'],
          'release_date' => date('Y-m-d H:i:s', $releaseDate),
          'format' => $_POST['format'],
          'actors' => $_POST['actors'] ?? []
        ];

        $validator = $this->movieService->validateCreate();

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $this->movieService->handleCreate($data);

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

            $this->movieService->handleDelete($data);

            redirectTo('/dashboard.php');
        }
    }
}