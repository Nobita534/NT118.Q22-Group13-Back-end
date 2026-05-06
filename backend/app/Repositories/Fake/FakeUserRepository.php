<?php

namespace App\Repositories\Fake;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class FakeUserRepository implements UserRepositoryInterface
{
    private const CACHE_KEY = 'techbyte:fake-users';

    public function findByEmail(string $email): ?array
    {
        return collect($this->users())->firstWhere('email', $email);
    }

    public function findByUsername(string $username): ?array
    {
        return collect($this->users())->firstWhere('username', $username);
    }

    public function findById(int $id): ?array
    {
        return collect($this->users())->firstWhere('id', $id);
    }

    public function create(array $data): array
    {
        $users = $this->users();
        $nextId = empty($users) ? 1 : (max(array_column($users, 'id')) + 1);

        $user = [
            'id' => $nextId,
            'name' => $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'] ?? 'user',
        ];

        $users[] = $user;
        $this->persist($users);

        return $user;
    }

    private function users(): array
    {
        $users = Cache::get(self::CACHE_KEY);

        if (! is_array($users)) {
            $users = [
                [
                    'id' => 1,
                    'name' => 'Nguyen Van A',
                    'username' => 'editor',
                    'email' => 'editor@techbyte.vn',
                    'password' => Hash::make('Password@123'),
                    'role' => 'user',
                ],
                [
                    'id' => 2,
                    'name' => 'Admin TechByte',
                    'username' => 'admin',
                    'email' => 'admin@techbyte.vn',
                    'password' => Hash::make('Password@123'),
                    'role' => 'admin',
                ],
                [
                    'id' => 3,
                    'name' => 'Guest Viewer',
                    'username' => 'guest',
                    'email' => 'guest@techbyte.vn',
                    'password' => Hash::make('Password@123'),
                    'role' => 'guest',
                ],
            ];

            $this->persist($users);
        }

        return $users;
    }

    private function persist(array $users): void
    {
        Cache::forever(self::CACHE_KEY, $users);
    }
}
