<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;

use function filter_var;
use function is_string;

class Date extends AbstractValidator
{
    /**
     * @var string
     */
    private $message = '{{ value }} is not a valid date.';

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        $time = strtotime($value);

        if (!$time) {
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