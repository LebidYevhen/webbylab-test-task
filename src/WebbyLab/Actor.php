<?php

namespace WebbyLab;

use WebbyLab\Validator\Rules\Regex;
use WebbyLab\Validator\Rules\Required;
use WebbyLab\Validator\Rules\StringLength;
use WebbyLab\Validator\Validator;

class Actor
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function create()
    {
        $fields = [
          'name' => $_POST['name'],
          'surname' => $_POST['surname'],
        ];

        $validator = new Validator([
          'name' => [
            new Required(),
            (new StringLength())->max(255),
            (new Regex())->pattern('/^[a-zA-Z,\-]+$/')->invalidMessage(
              'Please note that only letters (both uppercase and lowercase), dashes, and commas are allowed in this field. Special characters, numbers, and spaces are not permitted.'
            )
          ],
          'surname' => [
            new Required(),
            (new StringLength())->max(255),
            (new Regex())->pattern('/^[a-zA-Z,\-]+$/')->invalidMessage(
              'Please note that only letters (both uppercase and lowercase), dashes, and commas are allowed in this field. Special characters, numbers, and spaces are not permitted.'
            )
          ],
        ]);

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $query = 'INSERT INTO actors(name, surname) VALUES(:name, :surname)';

            $this->database->query($query, [
              'name' => $data['name'],
              'surname' => $data['surname'],
            ]);

            session_start();
            $_SESSION['successStatus'] = [
              'success' => true,
              'message' => "1 Actor created."
            ];

            redirectTo('/add-actor.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }
}