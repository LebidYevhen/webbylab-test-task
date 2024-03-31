<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;

class Required extends AbstractValidator
{
    private $message = 'This field is required.';

    public function validate($value): bool
    {
        if (empty($value)) {
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