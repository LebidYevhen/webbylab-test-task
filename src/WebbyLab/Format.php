<?php

namespace WebbyLab;

use WebbyLab\Validator\Rules\IsFormatExists;
use WebbyLab\Validator\Rules\Regex;
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
          'name' => [
            new Required(),
            (new StringLength())->max(255),
            new IsFormatExists(),
            (new Regex())->pattern('/\b(VHS|DVD|Blue-Ray)\b/')->invalidMessage(
              'Please note that only words "VHS, DVD, Blue-Ray" are allowed in this field. Other words are not permitted.'
            )
          ],
        ]);

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $query = 'INSERT INTO formats(name) VALUES(:name)';

            $this->database->query($query, [
              'name' => $data['name'],
            ]);

            session_start();
            $_SESSION['successStatus'] = [
              'success' => true,
              'message' => "1 Format created."
            ];

            redirectTo('/add-format.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }
}