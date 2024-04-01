<?php

namespace WebbyLab;

use WebbyLab\Validator\Rules\Required;
use WebbyLab\Validator\Rules\StringLength;
use WebbyLab\Validator\Validator;

class Format
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
        ];

        $validator = new Validator([
          'name' => [new Required(), (new StringLength())->max(255)],
        ]);

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $query = 'INSERT INTO formats(name) VALUES(:name)';

            $this->database->query($query, [
              'name' => $data['name'],
            ]);

            redirectTo('/add-format.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }
}