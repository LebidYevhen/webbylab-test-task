<?php

namespace WebbyLab\Services;

use PDO;
use PDOException;
use WebbyLab\Database;
use WebbyLab\Validator\Rules\Date;
use WebbyLab\Validator\Rules\Required;
use WebbyLab\Validator\Validator;

class MovieService
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function validateCreate(): Validator
    {
        return new Validator([
          'name' => [new Required()],
          'release_date' => [new Required(), new Date()],
          'format' => [new Required()],
          'actors' => [new Required()]
        ]);
    }

    public function handleCreate(array $data): void
    {
        try {
            $this->database->connection->beginTransaction();

            $movieId = $this->insertMovie($data);

            $this->insertActors($movieId, $data['actors']);

            $this->database->connection->commit();
        } catch (PDOException $e) {
            $this->database->connection->rollBack();
            echo 'Movie creation error: '.$e->getMessage();
        }
    }

    public function validateDelete()
    {
        return new Validator([
          'movie_id' => [new Required()],
        ]);
    }

    public function handleDelete(array $data): void
    {
        $query = 'DELETE FROM movies WHERE id = :id';
        $this->database->query($query, [
          'id' => $data['movie_id']
        ]);
    }

    public function insertMovie(array $data)
    {
        $query = 'INSERT INTO movies(name, release_date, format_id, user_id) VALUES(:name, :release_date, :format_id, :user_id)';

        $this->database->query($query, [
          'name' => $data['name'],
          'release_date' => $data['release_date'],
          'format_id' => $data['format'],
          'user_id' => $_SESSION['user'],
        ]);

        return $this->database->id();
    }

    public function insertActors(int $movieId, array $actorsIds)
    {
        foreach ($actorsIds as $id) {
            $this->database->query('INSERT INTO movie_actors(movie_id, actor_id) VALUES(:movie_id, :actor_id)', [
              'movie_id' => $movieId,
              'actor_id' => $id
            ]);
        }
    }

    public function getFormats()
    {
        return $this->database->query('SELECT * FROM formats')->findAll();
    }

    public function getActors($fields = '*')
    {
        return $this->database->query("SELECT $fields FROM actors")->findAll();
    }

    public function getMovies($searchTerm = null)
    {
        $perPage = 10;
        $page = $_GET['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT COUNT(*) FROM movies';
        if ($searchTerm) {
            $query .= " WHERE name LIKE '%$searchTerm%' ";
        }
        $stmt = $this->database->query($query);
        $total = $stmt->count();

        $totalPages = ceil($total / $perPage);

        $query = '
            SELECT m.id, m.name, m.release_date, f.name AS format_name, GROUP_CONCAT(a.name SEPARATOR ", ") AS actor_names
            FROM movies m
            INNER JOIN formats f ON m.format_id = f.id
            LEFT JOIN movie_actors ma ON m.id = ma.movie_id
            LEFT JOIN actors a ON ma.actor_id = a.id';

        if ($searchTerm) {
            $query .= " WHERE m.name LIKE '%$searchTerm%' ";
        }

        $query .= '
            GROUP BY m.id
            ORDER BY m.name
            LIMIT :offset, :per_page';

        $stmt = $this->database->connection->prepare($query);

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $perPage, PDO::PARAM_INT);

        $stmt->execute();
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
          'movies' => $movies,
          'totalPages' => $totalPages
        ];
    }
}