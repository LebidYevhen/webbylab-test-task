<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;
use WebbyLab\Database;

use function filter_var;
use function is_string;

class IsEmailTaken extends AbstractValidator
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    /**
     * @var string
     */
    private $message = '{{ value }} is taken.';

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        $emailCount = $this->database->query(
          "SELECT COUNT(*) FROM users WHERE email = :email",
          [
            'email' => $value,
          ],
        )->count();

        if ($emailCount > 0) {
            $this->error($this->message, ['value' => $value]);
            return false;
        }

        return true;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}