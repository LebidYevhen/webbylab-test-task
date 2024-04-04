<?php

namespace WebbyLab\Services;

use PDO;
use PDOException;
use stdClass;
use WebbyLab\Database;
use WebbyLab\Validator\Rules\Date;
use WebbyLab\Validator\Rules\FileUpload;
use WebbyLab\Validator\Rules\Integer;
use WebbyLab\Validator\Rules\IsMovieNameTaken;
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
          'name' => [new Required(), new IsMovieNameTaken()],
          'release_date' => [new Required(), (new Integer())->min(1850)->max(2024)],
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
          'name' => htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'),
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

    public function getActors($fields = '*', $orderBy = 'name')
    {
        return $this->database->query("SELECT $fields FROM actors ORDER BY $orderBy")->findAll();
    }

    public function getMovies($searchTerm = null)
    {
        $perPage = 10;
        $page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $perPage;

        $query = 'SELECT COUNT(*) FROM movies';
        if ($searchTerm) {
            $query .= " WHERE name LIKE '%$searchTerm%' ";
        }
        $stmt = $this->database->query($query);
        $total = $stmt->count();

        $totalPages = ceil($total / $perPage);

        $query = '
            SELECT
                m.id,
                m.name,
                m.release_date,
                f.name AS format_name,
                GROUP_CONCAT(CONCAT(a.name, " ", a.surname) SEPARATOR ", ") AS actor_names
            FROM
                movies m
            INNER JOIN
                formats f ON m.format_id = f.id
            LEFT JOIN
                movie_actors ma ON m.id = ma.movie_id
            LEFT JOIN
                actors a ON ma.actor_id = a.id';

        if ($searchTerm) {
            $query .= " 
            WHERE
                m.name LIKE '%$searchTerm%' OR
                CONCAT(a.name, ' ', a.surname) LIKE '%$searchTerm%'
            ";
        }

        $query .= "
            GROUP BY
                m.id, m.name, m.release_date, f.name
            ORDER BY
                m.name
            LIMIT
                :offset, :per_page";

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

    public function validateImport(): Validator
    {
        return new Validator([
          'file' => [(new FileUpload())->extensions(['txt'])]
        ]);
    }

    public function importMovies()
    {
        $fields = [
          'file' => $_FILES['file'],
        ];

        $validator = $this->validateImport();

        if ($validator->validate($fields) === true) {
            $items = $this->extractParts();

            $moviesCounter = 0;
            $newActorsIds = [];
            $newFormatsIds = [];
            foreach ($items as $item) {
                $actorsIds = $this->extractAuthors($item->actors);
                $format = $this->extractFormat($item->format);
                $movieCount = $this->database->query(
                  "SELECT COUNT(*) FROM movies WHERE name = :name",
                  [
                    'name' => $item->name,
                  ],
                )->count();
                if ($movieCount > 0) {
                    continue;
                }
                $data = [
                  'name' => $item->name,
                  'release_date' => $item->release_date,
                  'format' => $format['id'],
                  'user_id' => $_SESSION['user'],
                  'actors' => $actorsIds['actorsIds']
                ];

                $moviesCounter++;
                if ($format['isNew']) {
                    $newFormatsIds[] = $format['id'];
                }
                $newActorsIds = array_merge($newActorsIds, $actorsIds['newActorsIds']);
                $this->handleCreate($data);
            }

            $actorsCounter = count(array_unique($newActorsIds));
            $formatsCounter = count(array_unique($newFormatsIds));

            $pluralizeMovie = $moviesCounter !== 1 ? 'Movies' : 'Movie';
            $pluralizeActor = $actorsCounter !== 1 ? 'Actors' : 'Actor';
            $pluralizeFormat = $formatsCounter !== 1 ? 'Formats' : 'Format';
            session_start();
            $_SESSION['successStatus'] = [
              'success' => true,
              'messages' => [
                'movies' => "$moviesCounter $pluralizeMovie created.",
                'actors' => "$actorsCounter $pluralizeActor created.",
                'formats' => "$formatsCounter $pluralizeFormat created."
              ]
            ];

            redirectTo('/import-movies.php');
        }

        return ['errors' => $validator->getErrors()];
    }

    public function extractParts()
    {
        $file_tmp = $_FILES["file"]["tmp_name"];
        $content = file($file_tmp, FILE_IGNORE_NEW_LINES);
        $content = array_diff($content, array(''));
        $rows = array_chunk($content, 4);

        return array_map(function ($array) {
            $item = new StdClass();
            $item->name = str_replace('Title: ', '', $array[0]);
            $item->release_date = str_replace('Release Year: ', '', $array[1]);
            $item->format = str_replace('Format: ', '', $array[2]);
            $item->actors = str_replace('Stars: ', '', $array[3]);
            return $item;
        }, $rows);
    }

    public function extractAuthors(string $authors)
    {
        $actors = explode(', ', $authors);
        $actorsIds = [];
        $newActorsIds = [];
        foreach ($actors as $actor) {
            $actor = explode(' ', $actor);
            $name = $actor[0];
            $surname = $actor[1];
            $actorId = null;

            $authorCount = $this->database->query(
              "SELECT COUNT(*) FROM actors WHERE name = :name AND surname = :surname",
              [
                'name' => $name,
                'surname' => $surname,
              ],
            )->count();

            if ($authorCount > 0) {
                $query = 'SELECT id FROM actors WHERE name = :name AND surname = :surname';

                $actorId = $this->database->query($query, [
                  'name' => $name,
                  'surname' => $surname,
                ])->find()['id'];
            } else {
                $query = 'INSERT INTO actors(name, surname) VALUES(:name, :surname)';

                $this->database->query($query, [
                  'name' => $name,
                  'surname' => $surname,
                ]);

                $actorId = $this->database->id();
                $newActorsIds[] = $actorId;
            }

            $actorsIds[] = $actorId;
        }

        return ['actorsIds' => $actorsIds, 'newActorsIds' => $newActorsIds];
    }

    public function extractFormat(string $formatName): array
    {
        $isNew = false;
        $query = 'SELECT * FROM formats where name = :format';
        $format = $this->database->query($query, [
          'format' => $formatName
        ])->find();

        if ($format) {
            $formatId = $format['id'];
        } else {
            $query = 'INSERT INTO formats(name) VALUES(:name)';

            $this->database->query($query, [
              'name' => $formatName,
            ]);

            $formatId = $this->database->id();
            $isNew = true;
        }

        return ['id' => $formatId, 'isNew' => $isNew];
    }

    public function isFormatExists($formatName): bool
    {
        $formatCount = $this->database->query(
          "SELECT COUNT(*) FROM formats WHERE name = :name",
          [
            'name' => $formatName,
          ],
        )->count();

        if ($formatCount > 0) {
            return true;
        }

        return false;
    }
}