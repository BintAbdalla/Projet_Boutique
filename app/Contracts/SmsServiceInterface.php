<?php

namespace App\Contracts;

interface SmsServiceInterface
{
    public function send(string $to, string $message): array;
}
