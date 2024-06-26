<?php

declare(strict_types=1);

namespace WebbyLab\Validator\Rules;

use WebbyLab\Validator\AbstractValidator;

class Date extends AbstractValidator
{

    private string $invalidMessage = 'Invalid type given. Date expected.';
    private string $minMessage = 'Date must be at least {{ limit }} year.';
    private string $maxMessage = 'Date cannot be greater than {{ limit }} year.';

    /**
     * @var int|null
     */
    private $min;
    /**
     * @var int|null
     */
    private $max;

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (!$value) {
            $this->error($this->invalidMessage, ['value' => $value]);
            return false;
        }

        $date = date_parse($value);

        if (is_int($this->min) && $date['year'] < $this->min) {
            $this->error($this->minMessage, ['value' => $value, 'limit' => $this->min]);
            return false;
        }

        if (is_int($this->max) && $date['year'] > $this->max) {
            $this->error($this->maxMessage, ['value' => $value, 'limit' => $this->max]);
            return false;
        }

        return true;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function invalidMessage(string $invalidMessage): self
    {
        $this->invalidMessage = $invalidMessage;
        return $this;
    }

    public function minMessage(string $minMessage): self
    {
        $this->minMessage = $minMessage;
        return $this;
    }

    public function maxMessage(string $maxMessage): self
    {
        $this->maxMessage = $maxMessage;
        return $this;
    }

    public function min(int $min): self
    {
        $this->min = $min;
        return $this;
    }

    public function max(int $max): self
    {
        $this->max = $max;
        return $this;
    }
}