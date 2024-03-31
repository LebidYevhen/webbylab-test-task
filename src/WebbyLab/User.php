<?php

namespace WebbyLab;

use WebbyLab\Services\UserService;

class User
{
    private Database $database;

    private UserService $userService;

    public function __construct()
    {
        $this->database = new Database();
        $this->userService = new UserService();
    }

    public function register()
    {
        $fields = [
          'email' => $_POST['email'],
          'password' => $_POST['password'],
        ];

        $validator = $this->userService->validateRegister();

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $this->userService->handleCreate($data);

            session_regenerate_id();

            $_SESSION['user'] = $this->database->id();

            redirectTo('/dashboard.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }

    public function login()
    {
        $fields = [
          'email' => $_POST['email'],
          'password' => $_POST['password'],
        ];

        $validator = $this->userService->validateLogin();

        if ($validator->validate($fields) === true) {
            $data = $validator->getData();

            $handleLogin = $this->userService->handleLogin($data);

            if ($handleLogin['success'] === false) {
                return ['errors' => ['email' => [$handleLogin['email']]], 'data' => $validator->getData()];
            }

            session_regenerate_id();

            $_SESSION['user'] = $handleLogin['user']['id'];

            redirectTo('/dashboard.php');
        }

        return ['errors' => $validator->getErrors(), 'data' => $validator->getData()];
    }

    public function logout()
    {
        unset($_SESSION['user']);

        session_destroy();

        session_regenerate_id();

        redirectTo('/login.php');
    }
}