<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthService
{
    private const RESET_PREFIX = 'techbyte:password-reset:';
    private const RESET_TTL_MINUTES = 60;

    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function login(string $email, string $password): array
    {
        // Tìm user từ Repository (giả định trả về mảng hoặc object)
        $userArray = $this->users->findByEmail($email);

        if (! $userArray || ! Hash::check($password, $userArray['password'])) {
            throw new \RuntimeException('Invalid credentials');
        }

        // Lấy ra Eloquent Model của User để dùng hàm tạo Token của Sanctum
        /** @var \App\Models\User $userModel */
        $userModel = \App\Models\User::find($userArray['id']);

        // Tạo token qua Sanctum
        $tokenResult = $userModel->createToken('techbyte-device-token');

        $name = $userArray['name'] ?? $userArray['username'] ?? null;
        $username = $userArray['username'] ?? $userArray['name'] ?? null;

        return [
            'access_token' => $tokenResult->plainTextToken, // Chuỗi token dạng Plain Text trả về cho Client
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 60) * 60, // 60 phút tính bằng giây
            'refresh_token' => null,
            'user' => [
                'id' => $userArray['id'],
                'name' => $name,
                'username' => $username,
                'email' => $userArray['email'],
                'role' => $userArray['role'],
            ],
        ];
    }

    public function register(string $username, string $email, string $password): array
    {
        if ($this->users->findByEmail($email)) {
            throw new \RuntimeException('EMAIL_TAKEN');
        }

        if ($this->users->findByUsername($username)) {
            throw new \RuntimeException('USERNAME_TAKEN');
        }

        $user = $this->users->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'user',
        ]);

        // Tạo token Sanctum ngay sau khi đăng ký thành công
        /** @var User $userModel */
        $userModel = User::find($user['id']);
        $tokenResult = $userModel->createToken('techbyte-device-token');

        return [
            'access_token' => $tokenResult->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 60) * 60,
            'refresh_token' => null,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'] ?? $user['username'],
                'username' => $user['username'] ?? $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    public function logout(?string $token): void
    {}

    public function forgotPassword(string $email): ?string
    {
        $user = $this->users->findByEmail($email);

        if (! $user) {
            return null;
        }

        $token = Str::random(64);

        Cache::put(self::RESET_PREFIX . $token, [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'issued_at' => now()->toIso8601String(),
        ], now()->addMinutes(self::RESET_TTL_MINUTES));

        return $token;
    }
}
