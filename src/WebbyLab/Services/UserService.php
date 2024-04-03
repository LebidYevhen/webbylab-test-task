<?php

namespace WebbyLab\Services;

use WebbyLab\Database;
use WebbyLab\Validator\Rules\Email;
use WebbyLab\Validator\Rules\IsEmailTaken;
use WebbyLab\Validator\Rules\Required;
use WebbyLab\Validator\Rules\StringLength;
use WebbyLab\Validator\Validator;

class UserService
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function validateRegister(): Validator
    {
        return new Validator([
          'email' => [new Required(), new Email(), (new StringLength())->max(255), new IsEmailTaken()],
          'password' => [new Required(), (new StringLength())->min(8)->max(255)]
        ]);
    }

    public function handleCreate(array $data)
    {
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $sql = 'INSERT INTO users(email, password) VALUES(:email, :password)';

        $this->database->query($sql, [
          'email' => $data['email'],
          'password' => $password,
        ]);

        return $this->database->id();
    }

    public function validateLogin(): Validator
    {
        return new Validator([
          'email' => [new Required(), new Email()],
          'password' => [new Required()]
        ]);
    }

    public function handleLogin(array $data)
    {
        $user = $this->database->query(
          "SELECT * FROM users WHERE email = :email",
          [
            'email' => $data['email']
          ]
        )->find();

        $passwordsMatch = password_verify($data['password'], $user['password'] ?? '');

        if (!$user || !$passwordsMatch) {
            return ['success' => false, 'password' => 'Invalid credentials.'];
        }

        return ['user' => $user];
    }
}