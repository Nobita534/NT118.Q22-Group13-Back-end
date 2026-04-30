<?php

namespace App\Repositories\Fake;

use App\Repositories\Contracts\UserRepositoryInterface;

class FakeUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?array
    {
        return collect($this->users())->firstWhere('email', $email);
    }

    public function findById(int $id): ?array
    {
        return collect($this->users())->firstWhere('id', $id);
    }

    private function users(): array
    {
        static $users;

        if ($users === null) {
            $users = [
                ['id' => 1, 'name' => 'Nguyen Van A', 'email' => 'editor@techbyte.vn', 'password' => 'Password@123', 'role' => 'user'],
                ['id' => 2, 'name' => 'Admin TechByte', 'email' => 'admin@techbyte.vn', 'password' => 'Password@123', 'role' => 'admin'],
                ['id' => 3, 'name' => 'Guest Viewer', 'email' => 'guest@techbyte.vn', 'password' => 'Password@123', 'role' => 'guest'],
            ];
        }

        return $users;
    }
}
