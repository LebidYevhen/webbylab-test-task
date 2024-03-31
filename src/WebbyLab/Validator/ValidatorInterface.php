<?php

namespace WebbyLab\Validator;

interface ValidatorInterface
{
    public function validate($value): bool;

    public function getError(): ?string;
}