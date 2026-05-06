<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?array;

    public function findByUsername(string $username): ?array;

    public function findById(int $id): ?array;

    public function create(array $data): array;
}
