<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?array
    {
        $user = User::where('Email', $email)->first();
        return $user ? $this->mapModelToArray($user) : null;
    }

    public function findByUsername(string $username): ?array
    {
        $user = User::where('Username', $username)->first();
        return $user ? $this->mapModelToArray($user) : null;
    }

    public function findById(int $id): ?array
    {
        $user = User::where('User_ID', $id)->first();
        return $user ? $this->mapModelToArray($user) : null;
    }

    public function create(array $data): array
    {
        // Map incoming keys to DB column names
        $payload = [];
        $payload['Username'] = $data['username'] ?? $data['name'] ?? null;
        $payload['Email'] = $data['email'] ?? null;
        // Hash password into PasswordHash column
        $payload['PasswordHash'] = isset($data['password']) ? Hash::make($data['password']) : null;
        $payload['Role'] = $data['role'] ?? 'user';
        $payload['Bio'] = $data['bio'] ?? null;
        $payload['Avatar'] = $data['avatar'] ?? null;

        $user = User::create($payload);

        return $this->mapModelToArray($user);
    }

    private function mapModelToArray(User $user): array
    {
        return [
            'id' => $user->User_ID,
            'name' => $user->Username ?? null,
            'username' => $user->Username ?? null,
            'email' => $user->Email ?? null,
            'password' => $user->PasswordHash ?? null,
            'role' => $user->Role ?? null,
            'bio' => $user->Bio ?? null,
            'avatar' => $user->Avatar ?? null,
        ];
    }
}
