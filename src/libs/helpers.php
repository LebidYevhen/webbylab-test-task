<?php

function view(string $filename, array $data = []): void
{
    foreach ($data as $key => $value) {
        $$key = $value;
    }
    require_once __DIR__.'/../includes/'.$filename.'.php';
}

function isPostRequest(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function isGetRequest(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
}

function redirectTo(string $url): void
{
    header('Location:'.$url);
    exit;
}

function dd(mixed $value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    die();
}