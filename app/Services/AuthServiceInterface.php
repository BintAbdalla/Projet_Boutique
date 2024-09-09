<?php

namespace App\Services;

interface AuthServiceInterface
{
    public function login(array $credentials);
    public function logout();
}
