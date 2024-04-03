<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;

use function ctype_digit;
use function is_int;
use function strval;

class Regex extends AbstractValidator
{
    /**
     * @var string
     */
    private string $invalidMessage;

    /**
     * @var string|null
     */
    private ?string $pattern;

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!preg_match($this->pattern, $value)) {
            $this->error($this->invalidMessage, ['value' => $value, 'invalidMessage' => $this->invalidMessage]
            );
            return false;
        }

        return true;
    }

    public function invalidMessage(string $invalidMessage): self
    {
        $this->invalidMessage = $invalidMessage;
        return $this;
    }

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

}