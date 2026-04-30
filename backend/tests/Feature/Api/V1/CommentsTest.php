<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class CommentsTest extends TestCase
{
    public function test_comment_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/comments', [
            'article_id' => 101,
            'content' => 'Nice article!',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_comment_store_succeeds_with_token(): void
    {
        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'editor@techbyte.vn',
            'password' => 'Password@123',
        ]);

        $token = $login->json('data.access_token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/comments', [
                'article_id' => 101,
                'content' => 'Bài viết rất hữu ích!',
            ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.article_id', 101);
    }
}
